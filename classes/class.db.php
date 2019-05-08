<?php

class S2N_DB_Functions{

    private $wpdb;

    function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->collate = $this->wpdb->get_charset_collate();
        $this->quoteTable = $this->wpdb->prefix . 's2n_quotes';
        $this->createTables();
    }



    public function returnRandomQuote(){
        return $this->wpdb->get_results( "SELECT `quotee`, `quote` FROM `{$this->quoteTable}` ORDER BY RAND() LIMIT 1", OBJECT );
    }

    public function returnAllQuotes(){
        //return all quotes not soft deleted for admin side
        //status 0 = active
        //status 1 = inactive
        //status 2 = softdelete
        return $this->wpdb->get_results( "SELECT `id`, `quotee`, `quote` , `status` ,`created`, `edited` FROM `{$this->quoteTable}` WHERE `status` != 2", OBJECT );
    }
    public function returnAllQuotees(){
        return $this->wpdb->get_results( "SELECT `id`, `quotee`, `quote` , `status` ,`created`, `edited` FROM `{$this->quoteTable}`", OBJECT );
    }

    public function returnQuoteByID( $id ){
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM `{$this->quoteTable}` WHERE `id`= %d", $id ), OBJECT );
    }

    public function returnQuotesByQuotee( $quotee ){
        return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM `{$this->quoteTable}` WHERE `quotee`= %s", $quotee ), OBJECT );
    }

    public function updateQuote( $args ){
        $update =  $this->wpdb->update( 
            $this->quoteTable, 
            [ 
                'quotee' => $args['quotee'] , 
                'quote' => $args['quote'] ,
                'status' => $args['status']
            ], 
            [ 'id' => $args['id'] ], 
            [  '%s' , '%s' , '%d' ], 
            [ '%d' ] 
        );
        return true;
    }

    public function deleteQuote( $id ){
        $delete = $this->wpdb->delete( $this->quoteTable, ['id' => $id ] );
        return true;
    }

    public function createQuote( $args ){
        $update = $this->wpdb->insert( 
            $this->quoteTable, 
            [ 
                'quotee' => $args['quotee'] , 
                'quote' => $args['quote'] ,
                'status' => $args['status']
                
            ], 
            [ '%s' , '%s' , '%d' ]
        );
        
        return true;
    }

    private function createTableIfNotExist( $table ){
        if( $this->wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {
           return true;
        }
        return false;
    }

    private function createTables(){
        if( $this->createTableIfNotExist( $this->quoteTable ) ){
            $this->createQuoteTable();
        }
        return true;
    }

    private function createQuoteTable(){
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->quoteTable}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `quotee` text NOT NULL,
            `quote` text NOT NULL,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `edited` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `status` int(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
          ) {$this->collate};
          COMMIT;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        return true;
    }
}