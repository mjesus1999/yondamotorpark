<?php

// $router->add('GET','/oc/pagos/{id}','PagosOCController','create'); // Llevara a la lista que em regitra pagos.
$router->add('POST','/oc/pagos/store','PagosOCController','store');