<div @class([
    'h-8' => ($data['size'] ?? 'md') === 'sm',
    'h-16' => ($data['size'] ?? 'md') === 'md',
    'h-32' => ($data['size'] ?? 'md') === 'lg',
])></div>
