<?php

return [
    'navigation' => [
        'groups' => [
            'tasks'         => 'Tasks',
            'time_tracking' => 'Time Tracking',
            'documents'     => 'Documents',
        ],
    ],
    'resources' => [
        'tasks' => [
            'label'  => 'Task',
            'plural' => 'Tasks',
            'sections' => [
                'details'    => 'Task Details',
                'assignment' => 'Assignment',
                'labels'     => 'Labels',
            ],
            'fields' => [
                'title'           => 'Title',
                'description'     => 'Description',
                'status'          => 'Status',
                'priority'        => 'Priority',
                'assignee'        => 'Assignee',
                'due_date'        => 'Due Date',
                'start_date'      => 'Start Date',
                'estimated_hours' => 'Estimated Hours',
                'labels'          => 'Labels',
            ],
            'columns' => [
                'title'           => 'Title',
                'status'          => 'Status',
                'priority'        => 'Priority',
                'assignee'        => 'Assignee',
                'due_date'        => 'Due Date',
                'estimated_hours' => 'Est. Hours',
            ],
        ],
        'task_labels' => [
            'label'  => 'Task Label',
            'plural' => 'Task Labels',
            'sections' => [
                'details' => 'Label Details',
            ],
            'fields' => [
                'name'  => 'Name',
                'color' => 'Color (hex)',
            ],
            'columns' => [
                'name'        => 'Name',
                'color'       => 'Color',
                'tasks_count' => 'Tasks',
            ],
        ],
        'time_entries' => [
            'label'  => 'Time Entry',
            'plural' => 'Time Entries',
            'sections' => [
                'details' => 'Time Entry Details',
            ],
            'fields' => [
                'task'        => 'Task',
                'entry_date'  => 'Entry Date',
                'description' => 'Description',
                'minutes'     => 'Time (minutes)',
                'is_billable' => 'Billable',
            ],
            'columns' => [
                'entry_date'  => 'Entry Date',
                'task'        => 'Task',
                'description' => 'Description',
                'duration'    => 'Duration',
                'is_billable' => 'Billable',
                'is_approved' => 'Approved',
            ],
            'filters' => [
                'is_billable'       => 'Billable',
                'billable_only'     => 'Billable only',
                'non_billable_only' => 'Non-billable only',
                'all_entries'       => 'All entries',
                'is_approved'       => 'Approval status',
                'approved_only'     => 'Approved only',
                'pending_only'      => 'Pending only',
            ],
        ],
        'timesheets' => [
            'label'  => 'Timesheet',
            'plural' => 'Timesheets',
            'sections' => [
                'details' => 'Timesheet Details',
            ],
            'fields' => [
                'week_start_date' => 'Week Starting',
                'status'          => 'Status',
                'submitted_at'    => 'Submitted',
            ],
            'columns' => [
                'week_start_date' => 'Week Starting',
                'status'          => 'Status',
                'submitted_at'    => 'Submitted',
            ],
        ],
        'document_folders' => [
            'label'  => 'Document Folder',
            'plural' => 'Document Folders',
            'sections' => [
                'details' => 'Folder Details',
            ],
            'fields' => [
                'name'             => 'Name',
                'parent_folder_id' => 'Parent Folder',
            ],
            'columns' => [
                'name'   => 'Name',
                'parent' => 'Parent',
            ],
        ],
        'documents' => [
            'label'  => 'Document',
            'plural' => 'Documents',
            'sections' => [
                'details' => 'Document Details',
            ],
            'fields' => [
                'title'     => 'Title',
                'folder_id' => 'Folder',
            ],
            'columns' => [
                'title'  => 'Title',
                'folder' => 'Folder',
            ],
        ],
    ],
];
