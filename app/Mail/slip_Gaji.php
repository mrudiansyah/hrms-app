<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class slip_Gaji extends Mailable
{
    use Queueable, SerializesModels;
    public $thn;
    public $bln;
    public $id_employee;
    public $tujuan;
    public $employee_name;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($thn,$bln,$id_employee,$tujuan,$employee_name)
    {
        $this->thn=$thn;
        $this->bln=$bln;
        $this->id_employee=$id_employee;
        $this->tujuan=$tujuan;
        $this->employee_name=$employee_name;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ems@summitadyawinsa.co.id')->view('slip_gaji');
    }
}