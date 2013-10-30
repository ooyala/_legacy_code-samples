This is a sample CodeIgniter app made for getting yourself running in no time. It is already integrated with the Ooyala PHP SDK (/application/third_party/php-v2-sdk) so there is no need to add anything else.

== What is CodeIgniter ==
CodeIgniter is a framework in PHP designed to have as low overhead as possible. It follows an MVC pattern, where you pass information to the view in the following way:
$this->view->load("name_of_the_view", $data)

The relevant files are structured in the following way:
/application
    /assets
    /config
    /controllers
    /views
    /libraries
    /models
    /third_party (Here you have extra stuff, e.g. Ooyala SDK)

== How to get it running ==
1. You need to have a server running that can interpret PHP, like Apache or Nginx.
2. Clone this repository in your server
3. Hit your local path like this "your_base_path/code-samples/api-examples/CodeIgniter_app/index.php"
For more information you can check http://ellislab.com/codeigniter/user-guide/installation/

== Structure ==
CodeIgniter follows a url path like

"base/controller/view/args"

