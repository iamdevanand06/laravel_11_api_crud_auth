<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use OpenSpout\Common\Entity\Row;
use App\Traits\commonTrait;
use App\Models\Product;
use App\Models\User;
use Validator;
use Storage;
use File;

class ExcelController extends Controller
{
    use commonTrait;

    public function downloadProduct(Request $request){

        try{
            $input = $request->all();

            $validator = Validator::make($input, [
                'extention' => 'required|max:5|min:3|regex:/^[a-zA-Z]+$/u',
            ]);

            $extension = '.xlsx';
            if($request->extention == 'csv'){
                $extension = '.csv';
            }

            $filename = 'Export-Products-'.date('Ymd-Hms').$extension;
            $products = Product::all();

            if(isset($products)){
                $writer = SimpleExcelWriter::streamDownload($filename);

                foreach($products as $key => $value){
                    $writer->addRow([
                        'S.No' => $key + 1,
                        'Name' => $value->name,
                        'Detail' => $value->detail,
                        'Capasity' => $value->capasity.' '.$value->capasity_type,
                        'Unit' => $value->unit,
                        'Price per Unit' => $value->price_per_unit,
                        'created_at' => $value->created_at
                    ]);

                    if ($key % 1000 === 0) {
                        flush(); // Flush the buffer every 1000 rows
                    }
                }

                $writer->toBrowser();

                return $this->sendResponse([], 'Download Product Excel successfully.');
            }
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }

    public function downloadUser(Request $request){

        try{
            $input = $request->all();

            $validator = Validator::make($input, [
                'extention' => 'required|max:5|min:3|regex:/^[a-zA-Z]+$/u',
            ]);

            $extension = '.xlsx';
            if($request->extention == 'csv'){
                $extension = '.csv';
            }

            $filename = 'Export-Users-'.date('Ymd-Hms').$extension;
            $users = User::all();

            if(isset($users)){
                $writer = SimpleExcelWriter::streamDownload($filename);

                foreach($users as $key => $value){
                    $writer->addRow([
                        'S.No' => $key + 1,
                        'Name' => $value->name,
                        'Email' => $value->email,
                        'Mobile Number' => $value->mobile_number,
                        'created_at' => $value->created_at
                    ]);

                    if ($key % 1000 === 0) {
                        flush(); // Flush the buffer every 1000 rows
                    }
                }

                $writer->toBrowser();

                return $this->sendResponse([], 'Download User Excel successfully.');
            }
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }

    public function uploadUser(Request $request){
        try{

            $input = $request->all();

            $validator = Validator::make($input, [
                'upload_file' => 'required|mimes:xlsx,csv|max:2048',
            ]);

            if ($file = $request->file('upload_file')) {
                $fileName =  'user-' . date('Ymd_His') . "." . $file->getClientOriginalExtension();
                Storage::disk('publicLocalUpload')->putFileAs('tempFile', $request->file('upload_file'), $fileName);
                $filePath = public_path('assets/uploadFile/tempFile/'.$fileName);
            }

            if ($this->fileExists($filePath) != 'false'){
                $rows = SimpleExcelReader::create($filePath)
                // ->useHeaders(['Name','Mobile Number','Email','Password','Locked','Status','Role ID'])
                ->fromSheetName("Sheet1")->getRows()
                ->each(function(array $userRow) {
                    // dd($userRow['status']);
                    User::create([
                        'name' => isset($userRow['Name'])?$userRow['Name']:'-',
                        'mobile_number' => isset($userRow['Mobile Number'])?$userRow['Mobile Number']:'-',
                        'email' => isset($userRow['Email'])?$userRow['Email']:'-',
                        'password' => isset($userRow['Password'])?$userRow['Password']:'-',
                        'is_locked' => isset($userRow['Locked'])?$userRow['Locked']:'0',
                        'status' => isset($userRow['Status'])?$userRow['Status']:'0',
                        'role_id' => isset($userRow['RoleID'])?$userRow['RoleID']:'-',
                    ]);
                });
                File::delete($filePath);
                return $this->sendResponse([], 'Users Excel has Upload been successfully.');
            }

        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }
}
