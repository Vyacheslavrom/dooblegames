<?php
  $page=NULL;

  function open_page($title, $description, $keywords)
  {
   global $page;

   $page = new Page($title, $description, $keywords);
   $page->open();

   return $page;
  }

  function close_page()
  {
   global $page;

   if ($page==NULL)
       return;


   $page->close();
   $page=NULL;
  }

  function get_page()
  {
    global $page;
    return $page;
  }

  class Page
  {
   private $_title;
   private $_description;
   private $_keywords;

   function __construct($title, $description, $keywords)
   {
     $this->_title = $title;
     $this->_description = $description;
     $this->_keywords = $keywords;
   }

   public function open()
   {
     echo "<!DOCTYPE HTML>\n";
     echo "<html>\n";
     echo "<head>\n";
     echo "<title>".$this->_title."</title>\n";
     echo "<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>\n";
     echo "<meta name='description' content='".$this->_description."'>\n";
     echo "<meta name='keywords' content='".$this->_keywords."'>\n";
     echo "<link rel='stylesheet' href='css/styles.css' type='text/css'>\n";
     echo "</head>\n";
     echo "<body>\n";
   }

   public function close()
   {
     echo "</body>\n";
     echo "</html>\n";
   }

   public function begin_header()
   {
     echo "<div class='frame'>\n";
     echo "<div class='header'>\n";
   }

   public function end_header()
   {
     echo "</div>\n";
     include "core/main_menu.html";
     include "core/rec.html";
     echo "<div class='main'>\n";
   }

   public function begin_left_side()
   {
     echo "<div class='main-side-left'>\n";
   }

   public function end_left_side()
   {
     echo "</div>\n";
   }

   public function begin_right_side()
   {
     echo "<div class='main-side-right'>\n";
   }

   public function end_right_side()
   {
     echo "</div>\n";
   }

   public function begin_center()
   {
     echo "<div class='main-center'>\n";
   }

   public function end_center()
   {
     echo "</div>\n";
   }

   public function begin_footer()
   {
     echo "</div>\n";
     echo "<div class='footer-proxy'></div>\n";
     echo "</div>\n";
     echo "<div class='footer'>\n";
   }

   public function end_footer()
   {
     echo "</div>\n";
   }
  }
?>
