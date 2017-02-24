<?php

/**
 * Class FormManager
 * @author  Tayfun Erbilen
 * @email   tayfunerbilen@gmail.com
 * @web     http://erbilen.net
 */
class FormManager
{

    // all elements
    public $formElements = [];

    // not required elements
    public $notRequiredElements = [];

    // all error's
    public $errors = [];

    // all form data's
    public $data = [];

    // form element type
    public $type = 'text';

    // is it necessary?
    public $required = true;

    // default submit button name
    public $submitBtn;

    // default post type
    public $postType = 'POST';

    // default label string
    public $label = null;

    // templates
    public $templates = [];

    // default value
    public $value = '';

    // Form html
    public $form = '';

    /**
     * @param $type
     * @param $template
     */
    public function template($type, $template)
    {
        $this->templates[$type] = $template();
    }

    /**
     * @param string $type
     */
    public function start($type = 'POST')
    {
        $this->submitBtn = null;
        $this->postType = $type;
        $this->form .= '<form action="" method="' . $type . '">';
    }

    public function end($print = true)
    {
        $this->form .= '</form>';
        if ($print) {
            echo $this->form;
        }
    }

    /**
     * @param $type
     * @return $this
     * Set element type
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param $required
     * @return $this
     * Set element required
     */
    public function required($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @param $str
     * @return $this
     */
    public function label($str)
    {
        $this->label = $str;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param $name
     * @param null $value
     * @param null $placeholder
     * Create input element
     */
    public function input($name, $placeholder = null)
    {
        $this->formElements[] = $name;
        $input = '<input id="input_' . $name . '" ' . ($this->required ? 'required="required"' : null) . ' type="' . $this->type . '" class="form-control" value="' . ($this->data($name) ? $this->data($name) : $this->value) . '" name="' . $name . '" placeholder="' . $placeholder . '">';
        if (isset($this->templates['input'])) {
            $this->form .= $this->replace('input', $input, 'input_' . $name);
        } else {
            $this->form .= '<div class="form-group">
                ' . $input . '
            </div>';
        }
        $this->clean($name);
    }

    /**
     * @param $name
     * @param null $value
     * @param null $placeholder
     * Create textarea element
     */
    public function textarea($name, $placeholder = null)
    {
        $this->formElements[] = $name;
        $textarea = '<textarea id="textarea_' . $name . '" ' . ($this->required ? 'required="required"' : null) . ' type="' . $this->type . '" class="form-control"name="' . $name . '" placeholder="' . $placeholder . '">' . ($this->data($name) ? $this->data($name) : $this->value) . '</textarea>';

        if (isset($this->templates['textarea'])) {
            $this->form .= $this->replace('textarea', $textarea, 'textarea_' . $name);
        } else {
            $this->form .= '<div class="form-group">' . $textarea . '</div>';
        }
        $this->clean($name);
    }

    /**
     * @param $name
     * @param $values
     */
    public function select($name, $values)
    {
        $this->formElements[] = $name;

        $select = '<select id="select_' . $name . '" ' . ($this->required ? 'required="required"' : null) . ' name="' . $name . ($this->type == 'multiple' ? '[]' : null) . '" ' . ($this->type != 'text' ? $this->type : null) . ' id="' . $name . '" class="form-control">';
        foreach ($values as $key => $val) {
            $select .= '<option ';

            // if is multiple
            if ($this->type == 'multiple') {
                if ($this->data($name) && isset(array_flip($this->data($name))[$key])) {
                    $select .= ' selected ';
                } elseif (!$this->data($name) && in_array($key, $this->value)) {
                    $select .= ' selected ';
                }
            } elseif ($this->type != 'multiple') {
                if ($this->data($name) == $key) {
                    $select .= ' selected ';
                } elseif (!$this->data($name) && $key == $this->value) {
                    $select .= ' selected ';
                }
            }

            $select .= ' value="' . $key . '">' . $val . '</option>';
        }
        $select .= '</select>';

        if (isset($this->templates['select'])) {
            $this->form .= $this->replace('select', $select, 'select_' . $name);
        } else {
            $this->form .= '<div class="form-group">' . $select . '</div>';
        }

        $this->clean($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function checkbox($name, $value)
    {
        $this->formElements[] = $name;

        $input = '<input ';
        if ($this->type == 'multiple') {
            if ($this->data($name) && isset(array_flip($this->data($name))[$value])) {
                $input .= ' checked ';
            } elseif (!$this->data($name) && in_array($value, $this->value)) {
                $input .= ' checked ';
            }
        } elseif ($this->type != 'multiple') {
            if ($this->data($name) == $value) {
                $input .= ' checked ';
            } elseif (!$this->data($name) && $this->value == $value) {
                $input .= ' checked ';
            }
        }
        $input .= 'type="checkbox" id="' . $name . '_' . $value . '" name="' . $name . ($this->type == 'multiple' ? '[]' : null) . '" value="' . $value . '">';

        if (isset($this->templates['checkbox'])) {
            $this->form .= $this->replace('checkbox', $input, $name . '_' . $value);
        } else {
            $this->form .= '<div class="checkbox">
                <label>' . $input . ' ' . $this->label . '</label>
            </div>';
        }

        if ($this->type == 'multiple') {
            if (!$this->data($name)) {
                $this->errors[] = $name;
            }
        }
        $this->clean($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function radio($name, $value)
    {
        $this->formElements[] = $name;

        $input = '<input ';
        if ($this->type == 'multiple') {
            if (isset(array_flip($this->data($name))[$value])) {
                $input .= ' checked ';
            } elseif (in_array($value, $this->value)) {
                $input .= ' checked ';
            }
        } elseif ($this->type != 'multiple') {
            if ($this->data($name) == $value) {
                $input .= ' checked ';
            } elseif ($value == $this->value) {
                $input .= ' checked ';
            }
        }
        $input .= 'type="radio" id="' . $name . '_' . $value . '" name="' . $name . ($this->type == 'multiple' ? '[]' : null) . '" value="' . $value . '">';

        if (isset($this->templates['radio'])) {
            $this->form .= $this->replace('radio', $input, $name . '_' . $value);
        } else {
            $this->form .= '<div class="checkbox">
                <label>' . $input . ' ' . $this->label . '</label>
            </div>';
        }
        if ($this->type == 'multiple') {
            if (!$this->data($name)) {
                $this->errors[] = $name;
            }
        }
        $this->clean($name);
    }

    /**
     * @param $name
     * @param string $class
     * Create submit button
     */
    public function submit($str, $name = 'submit', $class = 'btn-default')
    {
        $this->submitBtn = $name;
        $this->form .= '<button type="submit" name="' . $name . '" value="1" class="btn ' . $class . '">' . $str . '</button>';
    }

    /**
     * @return array|bool
     */
    public function control()
    {
        foreach ($this->formElements as $element) {
            if (!post($element) && !isset($this->notRequiredElements[$element])) {
                $this->errors[] = $element;
            } else {
                $this->data[$element] = $this->data($element);
            }
        }
        if ($this->errors)
            return false;
        return $this->data;
    }

    /**
     * @param $name
     * @return array|bool|string
     */
    public function data($name)
    {
        if (strtoupper($this->postType) == 'POST') {
            return $this::post($name);
        }
        return $this::get($name);
    }

    /**
     * @return string
     */
    public function error()
    {
        if (!$this->data($this->submitBtn))
            return false;
        return '<div class="alert alert-danger">Missing fields: ' . implode(', ', array_unique($this->errors)) . '</div>';
    }

    /**
     * @param $name
     * @return array|bool|string
     */
    public static function post($name)
    {
        if (isset($_POST[$name])) {
            if (is_array($_POST[$name])) {
                return array_map(function ($item) {
                    return htmlspecialchars(trim($item));
                }, $_POST[$name]);
            }
            return htmlspecialchars(trim($_POST[$name]));
        }
        return false;
    }

    /**
     * @param $name
     * @return array|bool|string
     */
    public static function get($name)
    {
        if (isset($_GET[$name])) {
            if (is_array($_GET[$name])) {
                return array_map(function ($item) {
                    return htmlspecialchars(trim($item));
                }, $_GET[$name]);
            }
            return htmlspecialchars(trim($_GET[$name]));
        }
        return false;
    }

    private function clean($name)
    {
        $this->type = 'text';
        if ($this->required == false) {
            $this->notRequiredElements[$name] = $name;
        }
        $this->required = true;
        $this->label = '';
        $this->value = '';
    }

    private function replace($type, $element, $name)
    {
        $this->form .= str_replace([
            '{label}',
            '{form}',
            '{name}'
        ], [
            $this->label,
            $element,
            $name
        ], $this->templates[$type]);
    }

}
