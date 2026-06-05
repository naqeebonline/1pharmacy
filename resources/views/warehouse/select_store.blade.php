<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }
        .store-card {
            transition: 0.3s;
            cursor: pointer;
        }
        .store-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        ul.store-list {
            list-style: none;
            padding: 0;
        }
        ul.store-list li {
            margin-bottom: 15px;
        }
        ul.store-list li a {
            display: block;
            padding: 12px 20px;
            background: white;
            text-decoration: none;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            color: #333;
            font-weight: 500;
            text-align: center;
        }
        ul.store-list li a:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4">Select a Store</h2>

    <div class="w-50 mx-auto">
        <ul class="store-list">
            @foreach($stores as $key => $value)
                <li>
                    <a href="{{ route('pos.set_user_store_id', [$value->id]) }}?type=next">
                        {{ $value->store_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
</body>
</html>
