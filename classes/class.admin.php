<?php

class S2N_Admin_Functions{

    private $db;

    function __construct(){
        if ( ! class_exists('S2N_DB_Functions') ) {
            include( plugin_dir_path( __FILE__ ) . 'class.db.php');
        }
        $this->db = new S2N_DB_Functions();
    }
    public function returnQuoteByID( $id ){
        return $this->db->returnQuoteByID( $id );
    }  
    public function saveQuote( $args ){
        return $this->db->updateQuote( $args );
    }  
    public function removeQuote( $quote ){
        return $this->db->deleteQuote( $quote );
    }  
    public function returnAuthorsList(){

        $html = '';
        $quotes = $this->db->returnAllQuotees();
        if( ! is_array( $quotes ) || empty( $quotes ) ){
            return 'No Current Authors';
        }
        $authors = [];
        foreach( $quotes as $quote ){
            array_push( $authors , $quote->quotee );        
        }

        $authors = array_unique( $authors );
        $html = '<ul>';
        foreach( $authors as $author ){
            $html .= '<li> <a href="/authors/' . $author . '">' . $author . '</a> </li>';
        }
        $html .= '</ul>';
        return $html;

    }
    public function returnAuthorQuotes( $author ){
        $html = '';
        $quotes = $this->db->returnQuotesByQuotee( $author );

        if( ! is_array( $quotes ) || empty( $quotes ) ){
            return 'No Current Quotes';
        }

        foreach( $quotes as $quote ){
            $html .= '
            <div class="quote-container">
                <p> ' . $quote->quote . ' </p>
            </div>';
        }
        return $html;
    }
    public function returnQuoteDisplay(){

        $random_quote = $this->db->returnRandomQuote();
        if( is_array( $random_quote ) && ! empty( $random_quote ) ){
            echo '
        <div class="quote-container">
            <p> ' . $random_quote[0]->quote . ' </p>
            <p> By: <a href="/authors/' . $random_quote[0]->quotee . '">' . $random_quote[0]->quotee . '</a> </p>
        </div>';
        }
        

    }
    
    public function checkPOST( $post , $id ){
        if( ! isset( $post['quote-quotee'] ) || $post['quote-quotee'] == '' ){
            add_settings_error(
                $id,
                'quotee' ,
                'Must Enter A Quotee',
                'error'
            );
            return false;
        }
        if( ! isset( $post['quote-text'] ) || $post['quote-text'] == '' ){
            add_settings_error(
                $id,
                'quote' ,
                'Must Enter A Quote',
                'error'
            );
            return false;
        }
        if( ! isset( $post['quote-status'] ) || $post['quote-status'] == '' ){
            add_settings_error(
                $id,
                'status' ,
                'Must Select A Status',
                'error'
            );
            return false;
        }
        return true;
    }
    public function addQuote( $args ){
        return $this->db->createQuote( $args );
    }

    public function returnQuotes(){
        return $this->db->returnAllQuotes();
    }
    
}