<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;

class LinkDoctorsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doctors:link-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link existing doctors to their user accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Linking doctors to user accounts...');
        
        // Get all doctors without UserID
        $doctors = Doctor::whereNull('UserID')->orWhere('UserID', '')->get();
        
        if ($doctors->isEmpty()) {
            $this->info('All doctors are already linked to user accounts.');
            return 0;
        }
        
        $this->info("Found {$doctors->count()} unlinked doctor(s).");
        
        // Get all doctor users
        $doctorUsers = DB::table('user')
            ->where('role', 'doctor')
            ->whereNotIn('UserID', function($query) {
                $query->select('UserID')
                    ->from('doctor')
                    ->whereNotNull('UserID');
            })
            ->get();
        
        if ($doctorUsers->isEmpty()) {
            $this->warn('No unlinked doctor users found. You may need to manually link doctors to users.');
            return 1;
        }
        
        $linked = 0;
        foreach ($doctors as $doctor) {
            // Try to find a user that matches (by checking if there's a pattern)
            // For now, we'll link them in order
            if ($doctorUsers->count() > $linked) {
                $user = $doctorUsers[$linked];
                $doctor->UserID = $user->UserID;
                $doctor->save();
                $this->info("Linked doctor {$doctor->DoctorID} ({$doctor->FullName}) to UserID {$user->UserID}");
                $linked++;
            }
        }
        
        $this->info("Successfully linked {$linked} doctor(s) to user accounts.");
        $this->warn('Please verify the links are correct. You may need to manually adjust them.');
        
        return 0;
    }
}
