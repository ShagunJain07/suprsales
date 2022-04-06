<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Auth;
use Response;

class MyOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //if the user has not logged in. Auth::user() is used to get the currently authenticated user.
        if (isset(Auth::user()->id)) {
        //it gives the emp_id with authenticated user ID
            $ids = Auth::user()->emp_id;
            $announcement = Http::get('http://localhost/suprsales_api/Announcement/create_announcement/getAnnouncementByRegion.php?id=' . $ids)->json();

            $ann = Http::get('http://localhost/suprsales_api/Auth_Reference/?id=' . $ids)->json();
        //isset $ann use for the reference should have some data as the createFarmer
            $count = 0;
            if (isset($ann)) {
                foreach ($ann as $val) {
                    if ($val['auth_reference'] == 'myorder') {
                        $count = 1;
                        break;
                    }
                }
            }
            //The compact() function is used to convert given variable to array in which the key of the array will be the name of the variable and the value of the array will be the value of the variable
            // it will make the array of announcement', 'ann',store it inside the  .

            if ($count == 1) {
                return view('myOrder')->with(compact('announcement', 'ann'));
            }
            // it will return 404 eror if the user is not authenticate
            else {
                return redirect('error');
            }
        }
        // otherwise it will return to the userlogin page
        else {
            return redirect('userlogin');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     //  It get the days from UI and store in $days
        $days = $request->get('days');

    // It is for two part date one is for start date one is for end date
        list($part1, $part2) = explode(' - ', $days);
    // It is for two part1 date is for start date it contain month,day,year
        list($month1, $day1, $year1) = explode('/', $part1);
    // It is for two part2 date is for end date it contain month,day,year
        list($month2, $day2, $year2) = explode('/', $part2);

        // declear start and end date by the year,month and day
        $start_date = $year1 . '-' . $month1 . '-' . $day1;
        $end_date = $year2 . '-' . $month2 . '-' . $day2;
       //if the user has not logged in. Auth::user() is used to get the currently authenticated user.
        $ids = Auth::user()->emp_id;
        //It shows authenticated uses references and store it in $ann
        $ann = Http::get('http://localhost/suprsales_api/Auth_Reference/?id=' . $ids)->json();
        // $admins contain all EMP_ID with There EMP_NAME get from the ui
        $admins = Http::get('http://localhost/suprsales_api/Employee/getEmp.php')->json();
        $announcement = Http::get('http://localhost/suprsales_api/Announcement/create_announcement/getAnnouncementByRegion.php?id=' . $ids)->json();
        // Here Emp_is  compare Authorise users at the $emps for the same user
        $emps = Auth::user()->emp_id;
        //$em = '['.$emps.']';
        // Then $order get the data for the $emp
        $order = Http::get('http://localhost/suprsales_api/Order/getOrderByLoginEmp.php?id=' . $emps . '&start_date=' . $start_date . '&end_date=' . $end_date)->json();

       // It shows the data for that only user as emp_id from start date to end date
        return view('myOrder', ['start_date' => $start_date], ['end_date' => $end_date])->with(compact('announcement', 'ann', 'order', 'admins'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
    }
    // This is use for showing the data for the particular user
    public function shows(Request $request, $id, $rid)
    {
     //  in $rid put the data by route 'rid'        $id = $request->route('myorder');
        $rid = $request->route('rid');
    // Then $orders contain details order get from  getOrderDetail . php inside Order inside suprsales_api
        $orders = Http::get('http://localhost/suprsales_api/Order/getOrderDetail.php?id=' . $id)->json();

    // the $order store as data inside $userdata
        $userData['data'] = $orders;
    //   Then encoded the userdata and shown to the user
        echo json_encode($userData);
        exit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //  If there is any update then it will work
    public function update(Request $request, $id)
    {
        // It takes start date and end date from the users
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        //$order contain all the order details with employee ids by the start  date it will call
        $order = Http::get('http://localhost/suprsales_api/Order/getAllEmpOrder.php?start_date=' . $start_date . '&end_date=' . $end_date)->json();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=myorders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        // It sets the column heading
        $columns = array('Order No', 'Customer Code', 'Customer Name', 'Date', 'Placed By', 'Total Ordered Value', 'Plant', 'Remarks', 'Status', 'Order Code', 'Description', 'Quantity', 'Price', 'Total Value');
        // $callback is a function which is passed as an argument into another function.
        $callback = function () use ($order, $columns) {
        // fopen is used to open a file or an URL. It is used to bind a resource to a steam using a specific filename
            $file = fopen('php://output', 'w');
        // to format a line as CSV($file, $columns) file and writes it to an open file
            fputcsv($file, $columns);

            foreach ($order as $review) {
        //to format a line as CSV($file, array) file and writes it to an open file
                fputcsv($file, array($review['ORDER_ID'], $review['CUSTOMER_ID'], $review['CUSTOMER_NAME'], $review['ORDER_DATE'], $review['PLACED_BY'], $review['TOTAL_ORDER_VALUE'], $review['PLANT_NAME'], $review['REMARKS'], $review['STATUS'], $review['SKU_ID'], $review['SKU_DESCRIPTION'], $review['SKU_QUANTITY'], $review['PRICE'], $review['TOTAL_SKU_VALUE']));
            }
         // after opening the file it help to close a file which is pointed by an open file pointer.
            fclose($file);
        };
        //dd($callback);
        // stream use to access many types of data using a common set of functions and tools.
        return Response::stream($callback, 200, $headers)->send();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
