<?php

return [
    'navigation' => [
        'groups' => [
            'invoices' => 'Invoices',
            'expenses' => 'Expenses',
            'reports'  => 'Reports',
        ],
    ],
    'resources' => [
        'invoices' => [
            'label'  => 'Invoice',
            'plural' => 'Invoices',
            'sections' => [
                'details' => 'Invoice Details',
                'pricing' => 'Pricing',
            ],
            'fields' => [
                'number'         => 'Number',
                'contact_id'     => 'Contact ID',
                'issue_date'     => 'Issue Date',
                'due_date'       => 'Due Date',
                'currency'       => 'Currency',
                'status'         => 'Status',
                'notes'          => 'Notes',
                'discount_type'  => 'Discount Type',
                'discount_value' => 'Discount Value',
                'tax_rate'       => 'Tax Rate',
            ],
            'columns' => [
                'number'     => 'Number',
                'contact'    => 'Contact',
                'issue_date' => 'Issue Date',
                'due_date'   => 'Due Date',
                'total'      => 'Total',
                'status'     => 'Status',
            ],
        ],
        'credit_notes' => [
            'label'  => 'Credit Note',
            'plural' => 'Credit Notes',
            'sections' => [
                'details' => 'Credit Note Details',
            ],
            'fields' => [
                'invoice_id' => 'Invoice',
                'number'     => 'Number',
                'amount'     => 'Amount',
                'reason'     => 'Reason',
                'issued_at'  => 'Issued At',
            ],
            'columns' => [
                'number'    => 'Number',
                'invoice'   => 'Invoice',
                'amount'    => 'Amount',
                'issued_at' => 'Issued At',
            ],
        ],
        'recurring_invoices' => [
            'label'  => 'Recurring Invoice',
            'plural' => 'Recurring Invoices',
            'sections' => [
                'details' => 'Recurring Invoice Details',
            ],
            'fields' => [
                'frequency'   => 'Frequency',
                'next_run_at' => 'Next Run At',
                'last_run_at' => 'Last Run At',
                'is_active'   => 'Active',
            ],
            'columns' => [
                'frequency'   => 'Frequency',
                'next_run_at' => 'Next Run',
                'last_run_at' => 'Last Run',
                'is_active'   => 'Active',
            ],
        ],
        'expenses' => [
            'label'  => 'Expense',
            'plural' => 'Expenses',
            'sections' => [
                'details' => 'Expense Details',
            ],
            'fields' => [
                'tenant_id'           => 'Employee',
                'expense_category_id' => 'Category',
                'description'         => 'Description',
                'amount'              => 'Amount',
                'currency'            => 'Currency',
                'expense_date'        => 'Expense Date',
                'status'              => 'Status',
                'vendor'              => 'Vendor',
                'mileage_km'          => 'Mileage (km)',
                'rejection_reason'    => 'Rejection Reason',
            ],
            'columns' => [
                'description'  => 'Description',
                'employee'     => 'Employee',
                'amount'       => 'Amount',
                'expense_date' => 'Expense Date',
                'status'       => 'Status',
                'category'     => 'Category',
            ],
            'actions' => [
                'approve'          => 'Approve',
                'reject'           => 'Reject',
                'rejection_reason' => 'Rejection Reason',
            ],
        ],
        'expense_categories' => [
            'label'  => 'Expense Category',
            'plural' => 'Expense Categories',
            'sections' => [
                'details' => 'Expense Category',
            ],
            'fields' => [
                'name'          => 'Name',
                'description'   => 'Description',
                'monthly_limit' => 'Monthly Limit',
                'is_active'     => 'Active',
            ],
            'columns' => [
                'name'          => 'Name',
                'description'   => 'Description',
                'monthly_limit' => 'Monthly Limit',
                'is_active'     => 'Active',
            ],
        ],
        'expense_reports' => [
            'label'  => 'Expense Report',
            'plural' => 'Expense Reports',
            'sections' => [
                'details' => 'Expense Report Details',
            ],
            'fields' => [
                'title'        => 'Title',
                'tenant_id'    => 'Employee',
                'status'       => 'Status',
                'submitted_at' => 'Submitted At',
            ],
            'columns' => [
                'title'        => 'Title',
                'employee'     => 'Employee',
                'status'       => 'Status',
                'submitted_at' => 'Submitted',
            ],
        ],
        'mileage_rates' => [
            'label'  => 'Mileage Rate',
            'plural' => 'Mileage Rates',
            'sections' => [
                'details' => 'Mileage Rate',
            ],
            'fields' => [
                'name'           => 'Name',
                'rate_per_km'    => 'Rate per km',
                'currency'       => 'Currency',
                'effective_from' => 'Effective From',
                'effective_to'   => 'Effective To',
                'is_active'      => 'Active',
            ],
            'columns' => [
                'name'           => 'Name',
                'rate_per_km'    => 'Rate per km',
                'currency'       => 'Currency',
                'effective_from' => 'Effective From',
                'effective_to'   => 'Effective To',
                'is_active'      => 'Active',
            ],
        ],
        'financial_reporting' => [
            'label'      => 'Financial Reports',
            'nav_label'  => 'Financial Reports',
            'page_title' => 'Financial Reports',
        ],
    ],
];
