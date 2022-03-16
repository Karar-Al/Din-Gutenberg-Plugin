<?php
/*
  Plugin Name: Demo Template
  @package Demo Template
*/

/**
 * "enqueue_block_editor_assets" körs när
 * det är dags att ladda in alla nya block.
 */
add_action('enqueue_block_editor_assets', 'demo_template_my_block_files');

function demo_template_my_block_files()
{
  /**
   * Lägg till vår nya block som finns
   * under "demo-template-block.js"
   */
  wp_enqueue_script(
    'demo-template-handle',
    plugin_dir_url(__FILE__) . 'demo-template-block.js',
    array('wp-blocks', 'wp-i18n', 'wp-editor'),
    null
  );
}

/**
 * "init" körs när plugin-et är igång för första gången.
 */
add_action('init', 'demo_template_register_block');


function demo_template_register_block()
{
  /**
   * Lägg till server-delen av vår block.
   * Se till att det första värdet stämmer överens
   * med det namn man angav under "demo-template-block.js"
   */
  register_block_type('demo-template/block', array(

    /**
     * Kör följande funktion när blocket
     * ska renderas på webbsidan.
     */
    'render_callback' => 'demo_template_frontend_block'
  ));
}

// tl;dr
// GET = Är synligt i URLen
// POST = Är inte synlig i URL:en, bara synlig i BODY:n.

function demo_template_frontend_block($attributes)
{
  $html_result = "Attributen från Gutenberg-block: {$attributes["min_attribut"]}";

  $host = 'localhost';
  $user = 'root';
  $passwd = '';
  $schema = 'databas_namn';

  $conn = new mysqli($host, $user, $passwd, $schema);

  if ($conn->connect_error) {
    echo ("Anslutning failar: " . $conn->connect_error);
  }

  if ($_POST != null) {
    /**
     * "Frontend" Användaren har skickat en
     * respons till oss via formuläret
     */

    // Mata in information till databas...

    // $sql_insert = "INSERT INTO tabell_namn () VALUES ()"

    // if ($conn->query($sql_insert) === TRUE) {
    //   $database_result .= "<p>Success! Matade in till databasen.</p>";
    // }
  }

  /*
  // Skapa en query som ska få tillbaka allting från tabellen.
  $sql_get_all = "SELECT * FROM tabell_namn";

  $result = $conn->query($sql_get_all);

  // Jag har fått tillbaka data från databasen.
  if ($result !== FALSE && $result->num_rows > 0) {
    // Visa datan.
    while ($row = $result->fetch_assoc()) {
      $html_result .= "<p>Id: " . $row["id"] . "<p>";
    }
  } else {
    // Fick ingenting tillbaka.
    $html_result = "<p>Tomt i databasen.</p>";
  }
  */

  // Stäng av databasen innan vi returnerar.
  $conn->close();
  return $html_result;
}

/**
 * "rest_api_init" körs när REST API:n är igång.
 * Alla REST API:n når man via:
 * http://localhost/wp/wp-json/
 */
add_action("rest_api_init", "demo_template_init_admin_api");

function demo_template_init_admin_api()
{
  /**
   * Registrera ett REST API på "demo-template/v1/admin"
   * Alltså på:
   * http://localhost/wp/wp-json/demo-template/v1/admin
   */
  register_rest_route('demo-template/v1', '/admin', array(
    // Denna REST API ska bara kallas via POST
    'methods' => 'POST',
    /**
     * Kör följande funktion när någon kallar på vår REST API.
     */
    'callback' => 'demo_template_admin_api'
  ));
}

function demo_template_admin_api($data)
{
  /**
   * $_POST & $_GET via
   * "$data->get_params()"
   */
  $request = $data->get_params();

  $host = 'localhost';
  $user = 'root';
  $passwd = '';
  $schema = 'databas_namn';

  $conn = new mysqli($host, $user, $passwd, $schema);

  // Gör admin saker!

  // Admin panelen vill ha lite data, eftersom de angav ?action=data i URLen.
  if ($request["action"] != null && $request["action"] === "data") {
     $test_sql = "SELECT * FROM boka";
    $result = $conn->query($test_sql);

    $dataToReturn = array();

    while ($row = $result->fetch_assoc()) {
      // Pusha till array-en! Funkar som dataToReturn.push(row) i JS.
      array_push($dataToReturn, $row);
    }

    // Glöm ej att alltid stänga connection till databasen!
    $conn->close();
    return rest_ensure_response($dataToReturn);
  }

  // Gör andra admin saker!
  // ...

  $conn->close(); // Glöm ej att alltid stänga connection till databasen!
  return rest_ensure_response("Hej");
}
