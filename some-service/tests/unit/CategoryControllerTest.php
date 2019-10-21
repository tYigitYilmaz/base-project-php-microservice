<?php

namespace Tests\Unit;


use App\Controller\ReminderController;

class CategoryControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateCategory()
    {
        $url_invoke = getenv('HOST').'/todo/some-service/web/category/createCategory';
        $category = [
            'name'  => 'dummy',
            'user_id'=> 1
        ];

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($category)));

        $ch = curl_init($url_invoke);

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($category));
        curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response);

        curl_close($ch);

        $this->assertEquals(201, $response->statusCode);
        $this->assertEquals(true, $response->success);
        $this->assertEquals(addslashes('Category is included in the category list..'), addslashes($response->messages[0]));
        $this->assertArrayHasKey('name', json_decode(json_encode($response->data), true));
        $this->assertArrayHasKey('user_id', json_decode(json_encode($response->data), true));
        $this->assertArrayHasKey('created_at', json_decode(json_encode($response->data), true));
    }

    public function testSelectCategory()
    {
        $url_invoke = getenv('HOST').'/todo/some-service/web/category/selectCategory/1';

        $ch = curl_init($url_invoke);

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ch);
        $response = json_decode($response);

        curl_close($ch);
        var_dump($response);

        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals(true, $response->success);
        $this->assertArrayHasKey('category_id', json_decode(json_encode($response->data), true));
        $this->assertArrayHasKey('name', json_decode(json_encode($response->data), true));
    }


    public function testListAllCategories()
    {
        $url_invoke = getenv('HOST').'/todo/some-service/web/category/listAllCategories';

        $ch = curl_init($url_invoke);

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($ch);
        $response = json_decode($response);

        curl_close($ch);

        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals(true, $response->success);
    }

    public function testDeleteCategory()
    {
        $url_invoke = getenv('HOST').'/todo/some-service/web/category/deleteCategory';
        $category = [
            'category_id'  => 1,
        ];

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($category)));

        $ch = curl_init($url_invoke);

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($category));
        curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response);

        curl_close($ch);

        $this->assertEquals(202, $response->statusCode);
        $this->assertEquals(true, $response->success);
        $this->assertEquals(addslashes('Category is deleted from the category list..'), addslashes($response->messages[0]));
    }
}