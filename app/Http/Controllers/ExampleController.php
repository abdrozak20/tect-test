<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;

class ExampleController extends Controller
{
    public function testOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
		    'first_line' => 'required',
		    'second_line' => 'required',
		]);

		if ($validator->fails())
		{
		    $res['code'] = 400;
		    $res['message'] = $validator->messages()->all();
		} else {
            $explode_first_line = array_map('intval', explode(' ', $request->first_line));
            $explode_second_line = array_map('intval', explode(' ', $request->second_line));
            if (count($explode_first_line) < 2 || count($explode_first_line) > 2) {
                $res['code'] = 400;
		        $res['message'] = 'must have 2 data in first line';
                return response()->json($res);
            }

            $n = $explode_first_line[0];
            $d = $explode_first_line[1];
            if ($n <= 1 || $n > 20000) {
                $res['code'] = 400;
		        $res['message'] = 'n not qualified';
                return response()->json($res);
            }

            if ($d <= 1 || $d > $n) {
                $res['code'] = 400;
		        $res['message'] = 'd not qualified';
                return response()->json($res);
            }

            sort($explode_second_line);
            $expenditure = $explode_second_line;
            $rs = "";
            for ($i=0; $i < $n-$d; $i++) {
                $data = array_slice($expenditure, $i, $d);
                $index = floor($d / 2);

                if ($d % 2 == 0) {
                    $median = ($data[$index-1] + $data[$index]) / 2;
                } else {
                    $median = $data[$index];
                }

                $index_next = intval($d+$i);
                $next = $expenditure[$index_next];
                if ($next >= 2 * $median) {
                    $day = $index_next + 1 ;
                    $rs = $rs . 'send email at day ' . $day . ' ';
                }
            }

            if (empty($rs)) {
                $rs = 'No email sended';
            }

            $res['code'] = 200;
		    $res['message'] = $rs;
        }
        return response()->json($res);
    }
}
