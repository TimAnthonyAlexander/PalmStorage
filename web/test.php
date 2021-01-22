<?php
namespace PalmStorage;
require("../vendor/autoload.php");

echo (new read)->search("mydatabase", "password||email||cool");
