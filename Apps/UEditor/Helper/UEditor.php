<?php

namespace Apps\UEditor\Helper;

class UEditor extends \H1Soft\H\Web\Plugin {

    static public function create($_name, $options = NULL) {
        $create_editor = '';
        if (is_array($_name)) {
            foreach ($_name as $value) {
                $options['textarea'] = $value;
                $create_editor .= "var editor_$value = UE.getEditor('$value'," . json_encode($options) . ");";
            }
        } else {
            $options['textarea'] = $_name;
            $create_editor .= "var editor_{$_name} = UE.getEditor('{$_name}'," . json_encode($options) . ");";
        }
        $basePath = self::assets(__CLASS__);
        //复制文件到 $basePath

        $assetPath = self::assetPath(__CLASS__);
//        dirname(dirname(__FILE__)).'/Resources/';
        if (!file_exists($basePath . '/ueditor.all.min.js')) {
            \H1Soft\H\Utils\File::recurse_copy(dirname(dirname(__FILE__)) . '/Resource/', $basePath);
        }


        $editor = <<<EOF
<script type="text/javascript" charset="utf-8" src="$assetPath/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="$assetPath/ueditor.all.min.js"></script>
<link rel="stylesheet" type="text/css" href="$assetPath/themes/default/ueditor.css"/>                
<script type="text/javascript">	;                
	$create_editor
</script>                
EOF;

        return $editor;
    }

}
