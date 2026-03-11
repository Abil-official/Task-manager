<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task Assigned</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
            color: #374151;
            line-height: 1.6;
        }
        .content h2 {
            font-size: 20px;
            color: #111827;
            margin-top: 0;
        }
        .task-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin: 24px 0;
        }
        .task-title {
            font-size: 18px;
            font-weight: 600;
            color: #4f46e5;
            margin: 0 0 12px 0;
        }
        .task-description {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        .button-container {
            text-align: center;
            margin-top: 32px;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Task Assigned</h1>
        </div>
        <div class="content">
            <h2>Hello!</h2>
            <p>You have been assigned to a new task in the <strong>Task Manager</strong>. Here are the details:</p>
            
            <div class="task-card">
                <p class="task-title">{{ $task->title }}</p>
                <p class="task-description">
                    {{ Str::limit($task->description, 150) }}
                </p>
            </div>

            <div class="button-container">
                <a href="{{ config('app.url') }}/tasks/{{ $task->id }}" class="button">View Task Details</a>
            </div>
            
            <p style="margin-top: 32px;">Good luck with your new assignment!</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Task Manager. All rights reserved.
        </div>
    </div>
</body>
</html>