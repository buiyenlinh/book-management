<?php

namespace App\Http;

trait InitData {
  public function responseSuccess($data, $message = '', $status = 200) {
    $response = [
      'success' => true,
      'status' => $status,
      'message' => $message,
      'data' => $data
    ];
    return response()->json($response, $status);
  }

  public function responseError($errors = [], $message = '', $status = 404) {
    $response = [
      'success' => false,
      'status' => $status,
      'message' => $message,
      'errors' => $errors
    ];

    return response()->json($response, $status);
  }
}


?>