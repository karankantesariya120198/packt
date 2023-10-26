<?php

    /**
     * This method is  used for pass error response.
     * @Created On: 25-10-2022;
     * @Update On : 25-10-2022;
     * @Author: Developer
     * @version: 1.0.0
    */

    function errorResponse($errorMsg, $errorCode, $statusCode)
    {
        return response()->json([
            'result' => 0,
            'statusCode' => $statusCode,
            'message' => $errorMsg,
            'errorCode' => $errorCode,
        ], $statusCode);
    }


    /**
     * This method is  used for pass success response.
     * @Created On: 25-10-2022;
     * @Update On : 25-10-2022;
     * @Author: Developer
     * @version: 1.0.0
    */

    function successResponse($message, $statuscode, $data = null)
    {
        return response()->json([
            'result' => 1,
            'statusCode' => $statuscode,
            'message' => $message,
            'data' => $data
        ], $statuscode);
    }
        
?>