<?php

/**
 *Файл с подключением к БД
 */
require_once("database.php");

/**
 * Функция для дебага
 *
 * @param mixed $var
 * @return void
 */
function debug($var)
{
    echo "<PRE>";
        var_dump($var);
    echo "</PRE>";
    die();
}

/**
 * Возвращает только id из всего массива
 *
 * @param array $item
 * @return int
 */
function get_ids($item)
{
    return $item['id'];
}

$response = [];

$db_educations = $pdo->query('SELECT id, name FROM education')->fetchAll();
$db_cities = $pdo->query('SELECT id, name FROM city')->fetchAll();

$educations =  isset($_POST['education']) && $_POST['education'] ? $_POST['education'] : array_map("get_ids", $db_educations);
$cities = isset($_POST['city']) && $_POST['city'] ? $_POST['city'] : array_map("get_ids", $db_cities);

$education_placeholders = implode(',', array_fill(0, count($educations), '?'));
$cities_placeholders = implode(',', array_fill(0, count($cities), '?'));

$res_all = $pdo->prepare('
    SELECT u.name AS name_user,
           e.name AS name_edu,
           GROUP_CONCAT(c.name SEPARATOR \', \') AS name_city
    FROM education e INNER JOIN user u
    ON e.id = u.education_id                                  
    INNER JOIN users_cities                        
    ON u.id = users_cities.user_id
    INNER JOIN city c
    ON users_cities.city_id = c.id
    WHERE (u.id IN (
      SELECT DISTINCT u.id
      FROM user u
      LEFT JOIN users_cities uc ON u.id = uc.user_id
      WHERE uc.city_id IN ('. $cities_placeholders .'))
    )
    AND (e.id IN ('. $education_placeholders .'))
    GROUP BY u.id;');

$res_all->execute(array_merge($cities, $educations));

$users_cities = $res_all->fetchAll();

if (!$_POST) {
    $response['educations'] = $db_educations;
    $response['cities'] = $db_cities;
}

$response['users_cities'] = $users_cities;

echo json_encode($response);
