/* GISearch.php
 * requires DOMDocument
 * requires php7.0-XML
 * -
 * Searches Google and returns a random image for your search string
 */

class gisearch {
  public $SRC;
  public $TITLE;
  
  // is_json: checks if string is JSON or not
  function is_json($TEXT) {
    json_decode($TEXT);
    return (json_last_error() == JSON_ERROR_NONE);
  }
  
  function get_images($SEARCH) {
    // set up http options
    $HTTP_OPTS = Array(
    'http' => Array(
      'method' => "GET",
      'header' => "Accept: text/html\r\n".
                  "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36\r\n"
      )
    );
    $HTTP_CONTEXT = stream_context_create($HTTP_OPTS);
    // set up the URL
    $URL = "https://www.google.com.au/search?q=". urlencode($SEARCH) ."&source=lnms&tbm=isch&biw=1440&bih=770";
  
    // grab the HTML
    $HTML = file_get_contents($URL, false, $HTTP_CONTEXT);
    
    // create a new DOM Document
    $HTML_DOC = new DOMDocument();
    
    // start of get rid of errors
    $ERRORS = libxml_use_internal_errors(true);
    
    // load HTML into DOM Document
    $HTML_DOC->loadHTML($HTML);
    
    // end of get rid of errors
    libxml_use_internal_errors($ERRORS);
    
    // find all the <div>'s (from Google Image Search)
    $DIVS = $HTML_DOC->getElementsByTagName('div');
    
    // create a dummy array (so it doesn't get lost in the loop)
    $IMAGES[] = Array();
    foreach ($DIVS as $DIV) {
      // get the line from div
      $JSON = $DIV->nodeValue;
      
      // check if the line is actually JSON
      if ((!$this->is_json($JSON)) || (trim($JSON) == '')) {
        continue;
      }
  
      // convert JSON to Array
      $IMAGE = json_decode($JSON, true);
  
      // push the detail we need into another Array for use later
      $IMAGES[] = Array(
        'src' => $IMAGE['ou'],
        'title' => $IMAGE['s'],
        'link' => $IMAGE['ru']
      );
    }
    // output the list of images
    return $IMAGES;
  }
  
  function select_image($SEARCH) {
    // get the images from Google
    $IMAGES = $this->get_images($SEARCH);
    
    // select a random image
    $IMAGE = $IMAGES[rand(1, sizeof($IMAGES))];
    // return the image
    return $IMAGE;
  }
  
  function __construct($SEARCH) {
    $IMAGE = $this->select_image($SEARCH);
    
    $this->SRC = $IMAGE['src'];
    $this->LINK = $IMAGE['link'];
    $this->TITLE = $IMAGE['title'];
  }
}
