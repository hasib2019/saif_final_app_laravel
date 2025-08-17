<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;

class FormSubmissionController extends Controller
{
    /**
     * Display a listing of form submissions.
     */
    public function index(Request $request)
    {
        $query = FormSubmission::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name, email, or company
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('company', 'LIKE', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }

    /**
     * Store a newly created form submission.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:contact,quote,support,general',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $submission = new FormSubmission();
        $submission->fill($request->only(['type', 'name', 'email', 'phone', 'company', 'subject', 'message']));
        
        // Set IP address and user agent
        $submission->ip_address = $request->ip();
        $submission->user_agent = $request->userAgent();
        $submission->status = 'pending';

        $submission->save();

        // Send notification email (optional - you can implement this later)
        // $this->sendNotificationEmail($submission);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully. We will get back to you soon.',
            'data' => $submission->only(['id', 'type', 'name', 'email', 'subject', 'created_at'])
        ], 201);
    }

    /**
     * Display the specified form submission.
     */
    public function show(string $id)
    {
        $submission = FormSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Form submission not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $submission
        ]);
    }

    /**
     * Update the specified form submission.
     */
    public function update(Request $request, string $id)
    {
        $submission = FormSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Form submission not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pending,in_progress,resolved,closed',
            'response_message' => 'nullable|string|max:2000',
            'is_read' => 'nullable|boolean',
            'is_important' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('status')) {
            $submission->status = $request->status;
        }
        
        if ($request->has('response_message')) {
            $submission->response_message = $request->response_message;
            $submission->responded_at = now();
        }
        
        if ($request->has('is_read')) {
            $submission->is_read = $request->is_read;
        }
        
        if ($request->has('is_important')) {
            $submission->is_important = $request->is_important;
        }

        $submission->save();

        return response()->json([
            'success' => true,
            'message' => 'Form submission updated successfully',
            'data' => $submission
        ]);
    }

    /**
     * Remove the specified form submission.
     */
    public function destroy(string $id)
    {
        $submission = FormSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Form submission not found'
            ], 404);
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form submission deleted successfully'
        ]);
    }

    /**
     * Get form submission statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => FormSubmission::count(),
            'pending' => FormSubmission::where('status', 'pending')->count(),
            'in_progress' => FormSubmission::where('status', 'in_progress')->count(),
            'resolved' => FormSubmission::where('status', 'resolved')->count(),
            'closed' => FormSubmission::where('status', 'closed')->count(),
            'by_type' => FormSubmission::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'recent' => FormSubmission::orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'type', 'name', 'email', 'subject', 'status', 'created_at'])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Send notification email for new form submission.
     * This is a placeholder method - implement based on your email setup.
     */
    private function sendNotificationEmail(FormSubmission $submission)
    {
        // Implement email notification logic here
        // You can use Laravel's Mail facade to send emails
        // Example:
        // Mail::to('admin@derown.com')->send(new NewFormSubmissionMail($submission));
    }
}
