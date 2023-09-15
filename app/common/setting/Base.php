<?php

namespace app\common\setting;

use think\facade\Config;

class Base
{
    public function _form()
    {
        $name = $this->{'name'};
        $label = $this->{'label'};
        $prefix = $this->{'prefix'};
        $children = $this->{'children'};
        foreach ($children as &$child) {
            $child['value'] = $this->_value($prefix . $name, $child);
        }
        return [
            'name' => $name,
            'label' => $label,
            'children' => $children
        ];
    }
    public function _value($name, $child)
    {
        $value = config($name . '.' . $child['name']);
        if (isset($child['value_convert'])) {
            $value = $child['value_convert']($value);
        }
        if ($value) {
            return $value;
        }
        return $child['value'];
    }
    public function _save($key, $value)
    {
        $form = $this->_form();
        $name = $form['name'];
        $children = $form['children'];
        $prefix = $this->{'prefix'};
        $content = '';
        foreach ($children as $child) {
            $content .= "# {$child['label']}\n";
            if ($child['name'] == $key) {
                $content .= "'{$key}'=>{$this->_format($child,$value)},\n";
            } else {
                $content .= "'{$child['name']}'=>{$this->_format($child,$child['value'])},\n";
            }
        }
        $content = "<?php\n\nreturn [\n{$content}];";
        $file = root_path() . 'config' . DIRECTORY_SEPARATOR . $prefix . $name . '.php';
        $fileContent = @file_get_contents($file);
        $state = file_put_contents($file, $content);
        if (!$state && $fileContent) {
            file_put_contents($file, $fileContent);
        }
        return $state;
    }
    public function _format($child, $value)
    {
        $format = isset($child['value_format']) ? $child['value_format'] : 'string';
        switch ($format) {
            case 'string':
                return "'{$value}'";
            case 'int':
                return (int)$value;
            case 'bool':
                return $value ? "true" : "false";
            case 'array':
                return "['" . implode("','", $value) . "']";
            case 'float':
                return (float)$value;
            case 'double':
                return (float)$value;
            default:
                return "{$child['value_format']}('$value')";
        }
    }
    public static function __callStatic($name, $arguments)
    {
        $class = new static();
        $func = '_' . $name;
        if (method_exists($class, $func)) {
            return $class->$func(...$arguments);
        }
    }
}
