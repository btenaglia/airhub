<?php
namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use PDF;
use View;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class MailService extends BaseService
{

    public function sendConfirmationOfAuthorizePayment($bookingData)
    {
        $data = [
            'passangerName' => $bookingData->passangerName,
            'userName' => $bookingData->userName,
            'userEmail' => $bookingData->userEmail,
        ];

        Mail::send('mails.authorize_payment', $data, function ($m) use ($bookingData) {
            $to = $bookingData->userEmail;
            $subject = 'Booking made';

            $m
                ->to($to, '')
                ->from('info@airhub.us')
                ->subject($subject);
        });
    }

    public function sendApprovedFlight($flight, $user)
    {

        $data = [
            'user' => $user,
            'userName' => $user->getCompleteName(),
            'flight' => $flight,
        ];

        Mail::send('mails.flight_approved', $data, function ($m) use ($user) {

            $to = $user->getEmail();
            $subject = 'Flight approved';

            $m
                ->to($to, '')
                ->from('info@airhub.us')
                ->subject($subject);
        });
    }

    public function sendApprovedBooking($bookingData)
    {

        $data = [
            'passangerName' => $bookingData->passangerName,
            'userName' => $bookingData->userName,
            'userEmail' => $bookingData->userEmail,
        ];

        Mail::send('mails.booking_approved', $data, function ($m) use ($bookingData) {

            $to = $bookingData->userEmail;
            $subject = 'Booking approved';

            $m
                ->to($to, '')
                ->from('info@airhub.us')
                ->subject($subject);
        });
    }

    public function sendRecoverPassword($sendData)
    {
        $data = [
            'email' => $sendData->Email,
            'newpassword' => $sendData->Newpassword,
        ];

        Mail::send('mails.recover_password', $data, function ($m) use ($sendData) {

            $m
                ->to($sendData->Email, '')
                ->from('alert@airhub.us', 'Airhub')
                ->subject('Airhub App - New Password !');
        });

        if (count(Mail::failures()) > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function sendTest()
    {

        $data = [
            'email' => 'juanjose75@gmail.com',
            'newpassword' => 'test',
        ];

        Mail::send('mails.test_send', $data, function ($m) {

            $m
                ->to('juanjose75@gmail.com', '')
                ->from('alert@airhub.us', 'Airhub')
                ->subject('Airhub Test !');
        });

        if (count(Mail::failures()) > 0) {
            return 'Fail';
        } else {
            return 'Ok';
        }
    }
    public function sendAdminEmailToConfirmation($datos)
    {
        $data = [
            "client" => $datos['email'],
        ];
        $user = $this->getCurrentUser();
        $user->email;

        Mail::send('mails.notification', $data, function ($msj) use ($user) {
            $msj->from('alert@airhub.us');
            $msj->subject('Notification Reservation seat');
            $msj->to($user->email);
            return true;
        });
    }
    public function sendButtonPaypal($reservation)
    {
        $data = [
            "link" => $reservation['link'],
            "ticket" => $reservation['ticket'],
            "price" => $reservation['price'],
        ];
        Mail::send('mails.payment_button', $data, function ($msj) use ($reservation) {
            $msj->from('alert@airhub.us');
            $msj->subject('passages');
            $msj->to([$reservation['email']]);
            return true;
        });
    }

    public function sendContact($info)
    {
        $data = [
            "name" => $info['name'],
            "email" => $info['email'],
            "phone" => $info['phone'],
            "query" => $info['query'],
        ];
        Mail::send('mails.contact', $data, function ($msj) use ($info) {
            $msj->from('alert@airhub.us');
            $msj->subject('passages');
            $msj->to(["rulo.samper@gmail.com"]);

            return true;
        });
    }
    public function sendPdf($info)
    {
        define('BUDGETS_DIR', public_path('uploads/pdf')); // I define this in a constants.php file

        if (!is_dir(BUDGETS_DIR)) {

            mkdir(BUDGETS_DIR, 0755, true);
        }

        $pdfPath = BUDGETS_DIR . '/invoice.pdf';
        // File::put($pdfPath, PDF::load($html, 'A4', 'portrait')->output());
        $data = [
            "pepe" => "luis".rand(0,100),
        ];
        $content = PDF::load(View::make('pdfs/templateInfo', $data), 'A4', 'portrait')->output();
      if(file_exists($pdfPath)){
        @unlink($pdfPath);
      }
      
        chown($pdfPath,465);
        File::put($pdfPath, $content);
        $data = [
            "name" => $info['dateRequest'],
            "email" => $info['dateRequest'],
            "phone" => $info['dateRequest'],
            "query" => $info['dateRequest'],
        ];
        Mail::send('mails.pdf', $data, function ($msj) use ($pdfPath) {
            $msj->from('alert@airhub.us');
            $msj->subject('passages');
            $msj->to(["rulo.samper@gmail.com"]);
            $msj->attach($pdfPath);
            return true;
        },true);
        return $info;
    }

    public function infoCharter($info)
    {

        $data = [
            "name" => $info['name'],
            "lastname" => $info['lastname'],
            "email" => $info['email'],
            "phone" => $info['phone'],
            "origin" => $info['origin'],
            "destination" => $info['destination'],
            "date" => $info['dateRequest'],
            "hour" => $info['timeRequest'],
            "passengers" => $info['passengers'],
        ];
        Mail::send('mails.charter', $data, function ($msj) use ($info) {
            $msj->from('alert@airhub.us');
            $msj->subject('passages');
            $msj->to(["m.koss@alliesair.com"]);

            return true;
        });
    }
}
