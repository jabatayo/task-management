<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Handle contact form submission
     */
    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        // Add user_id if authenticated
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        // Store in database using Contact model
        Contact::create($data);

        // Optionally, send an email notification (uncomment and configure mail if needed)
        // Mail::to(config('mail.from.address'))->send(new ContactFormMail($data));

        return response()->json(['message' => 'Thank you for contacting us! We will get back to you soon.']);
    }

    /**
     * Return static about page info
     */
    public function about()
    {
        return response()->json([
            'name' => 'Task Management System',
            'version' => '1.0.0',
            'description' => 'A modern, secure, and robust task management platform built with Laravel and React. This application provides comprehensive task management capabilities with role-based access control, real-time updates, and intuitive user interface.',
            'features' => [
                'Task Creation and Management',
                'Role-based Access Control',
                'Priority and Status Tracking',
                'Due Date Management',
                'Search and Filtering',
                'Dashboard Analytics',
                'Responsive Design',
                'Real-time Updates',
                'User Authentication',
                'Contact Support System'
            ],
            'team' => [
                [
                    'name' => 'Jonathan',
                    'role' => 'Lead Developer',
                    'email' => 'jonathan@taskmanagement.com'
                ],
                [
                    'name' => 'Development Team',
                    'role' => 'Full Stack Developers',
                    'email' => 'dev@taskmanagement.com'
                ]
            ],
            'contact' => [
                'email' => 'jabatayo@gmail.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Task Management Ave, Tech City, TC 12345'
            ]
        ]);
    }
}
