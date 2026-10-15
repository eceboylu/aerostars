<x-mail::message>
# New entry from {{ $md($entry['company']) }}

Submitted {{ now()->format('d.m.Y H:i') }}. Reply to this email to answer the applicant directly.

## Categories ({{ count($categories) }})

@foreach ($categories as $category)
- **{{ $md($category['title']) }}** — {{ $md($category['family']) }}
@endforeach

## Company

<x-mail::table>
| Field | Value |
| :---- | :---- |
| Company name | {{ $md($entry['company']) }} |
| Website | {{ $md($entry['website']) }} |
| Contact name | {{ $md($entry['contact_name']) }} |
| Email | {{ $md($entry['email']) }} |
| Country | {{ $md($entry['country']) }} |
| Type of company | {{ $md($entry['company_type']) }} |
</x-mail::table>

## Why should this company win?

<div style="white-space:normal">{!! nl2br(e($entry['description'])) !!}</div>

## Supporting files ({{ count($fileNames) }})

@forelse ($fileNames as $fileName)
- {{ $md($fileName) }}
@empty
No files were uploaded.
@endforelse

The applicant confirmed the information is accurate and that they are authorised to enter on behalf of the company.
</x-mail::message>
