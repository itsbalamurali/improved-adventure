<?php


//	echo "yes" if the fields value is valid (and "no" if not)$val = $_GET['value'];

    switch ($_GET['name']) {
        case 'required':
            echo ('' !== $val) ? 'yes' : 'no';

            break;

        case 'number':
            if ('' === $val) {
                echo 'yes';

                break;
            }
            echo (is_numeric($val)) ? 'yes' : 'no';

            break;

        case 'email':
            if ('' === $val) {
                echo 'yes';

                break;
            }
            echo (preg_match('/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,3})$/Di', $val)) ? 'yes' : 'no';

            break;

        case 'url':
            if ('' === $val) {
                echo 'yes';

                break;
            }
            $pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
            echo (preg_match($pattern, $val)) ? 'yes' : 'no';

            break;

        default:
            echo 'yes';
    }
