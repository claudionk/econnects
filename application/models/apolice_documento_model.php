<?php
class Apolice_Documento_Model extends MY_Model
{
    //Dados da tabela e chave primária
    protected $_table      = 'apolice_documento';
    protected $primary_key = 'apolice_documento_id';

    //Configurações
    protected $return_type = 'array';
    protected $soft_delete = true;

    //Chaves
    protected $soft_delete_key = 'deletado';
    protected $update_at_key   = 'alteracao';
    protected $create_at_key   = 'criacao';

    //campos para transformação em maiusculo e minusculo
    protected $fields_lowercase = array();
    protected $fields_uppercase = array('nome');

    //Dados
    public $validate = array(
        array(
            'field'   => 'apolice_id',
            'label'   => 'Documento',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'apolice',
        ),
        array(
            'field'   => 'produto_parceiro_plano_tipo_documento_id',
            'label'   => 'Tipo de Documento / Plano',
            'rules'   => 'required',
            'groups'  => 'default',
            'foreign' => 'produto_parceiro_plano_tipo_documento',
        ),
        array(
            'field'  => 'name',
            'label'  => 'Nome',
            'rules'  => 'required',
            'groups' => 'default',
        ),
        array(
            'field'  => 'path',
            'label'  => 'Caminho',
            'groups' => 'default',
        ),
    );

    function with_tipo_documento(){
        $this->with_simple_relation('tipo_documento', '', 'tipo_documento_id', array('tipo_documento_id', 'nome', 'slug'));
        return $this;
    }

    public function get_by_id($id)
    {
        return $this->get($id);
    }

    public function filter_by_apolice($apolice_id, $tipo_documento_id = null)
    {
        $this->_database->join('produto_parceiro_plano_tipo_documento ppptd', "{$this->_table}.produto_parceiro_plano_tipo_documento_id = ppptd.produto_parceiro_plano_tipo_documento_id");
        $this->_database->where("{$this->_table}.apolice_id", $apolice_id);
        $this->_database->where("{$this->_table}.deletado", 0);

        if (!empty($tipo_documento_id)) {
            $this->_database->where("ppptd.tipo_documento_id", $tipo_documento_id);
        }

        return $this;
    }

    public function disableDoc($apolice_id, $tipo_documento_id = null){
        $docs = $this->filter_by_apolice($apolice_id, $tipo_documento_id)->get_all();

        foreach ($docs as $doc) {
            $this->update($doc[$this->primary_key], ['deletado' => 1, 'alteracao' => date('Y-m-d H:i:s')], TRUE);
        }
    
    }

    public function uploadFile($docFilename, $apolice_id, $produto_parceiro_plano_tipo_documento_id, $Content, $doImageResize = true) {
        $destinationFolder = app_assets_dir('documentos', 'uploads');

        if(!file_exists($destinationFolder)){
            mkdir($destinationFolder, 0777, true);
        }

        $dt = date( "Ymd_His" );

        if( strpos( $docFilename, "." ) ) {
            $Extension = strtolower( substr( $docFilename, strpos( $docFilename, "." ) + 1 ) );
        } else {
            $Extension = "unknown";
        }

        $Content = base64_decode( $Content );
        $Filename = $destinationFolder . $docFilename;

        $fp = fopen( $Filename , "w" );
        fwrite($fp, $Content );
        fclose($fp);

        $video = in_array($Extension, ['mp4','avi','wmv','flv','mov','mpg','mp2','mpeg','mpe','mpv']);
        if( !$video && $doImageResize ) {
            $img = $this->imageResize( $Filename, $Extension );
            if( $img ) {
                switch( $Extension ) {
                case "jpg":
                case "jpeg":
                imagejpeg( $img, $Filename );
                break;
                case "gif":
                imagegif( $img, $Filename );
                break;
                case "png":
                imagepng( $img, $Filename );
                break;
                }
            }
        }

        // insere o documento na Apolice
        $data = [
            'apolice_id' => $apolice_id,
            'produto_parceiro_plano_tipo_documento_id' => $produto_parceiro_plano_tipo_documento_id,
            'name' => $docFilename
        ];

        $apolice_documento_id = $this->insert($data, TRUE);

        return $apolice_documento_id;
    }

    //
    // Função para redefinir o tamanho de uma imagem JPEG
    //
    private function imageResize( $imageFilename, $imageType ) {
        $maxWidth = 1024;
        $maxHeight = 1024;
        list( $imageWidth, $imageHeight ) = getimagesize( $imageFilename );

        if( $imageWidth > $maxWidth && $imageHeight > $maxHeight ) {
            if( $imageWidth > $imageHeight ) {
                if( $imageWidth > $maxWidth ) {
                    $newHeight = $imageHeight * ( $maxWidth / $imageWidth );
                    $newWidth = $maxWidth;
                }
            } else {
                if( $imageHeight > $maxHeight ) {
                    $newWidth = $imageWidth * ( $maxHeight / $imageHeight );
                    $newHeight = $maxHeight ;
                }
            }

            switch( $imageType ) {
                case "jpg":
                    $source = imagecreatefromjpeg( $imageFilename );
                    break;
                case "jpeg":
                    $source = imagecreatefromjpeg( $imageFilename );
                    break;
                case "gif":
                    $source = imagecreatefromgif( $imageFilename );
                    break;
                case "png":
                    $source = imagecreatefrompng( $imageFilename );
                    break;
            }
            $destination = imagecreatetruecolor( $newWidth, $newHeight );
            imagecopyresampled( $destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight );
            return $destination;
        } else {
            return false;
        }
    }

}
