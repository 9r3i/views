<?php
(new class{
  const version='2.0.0';
  protected $user;
  protected $font;
  protected $color;
  protected $monitor;
  /* __construct */
  function __construct(){
    /* configuring user, font and color */
    $this->user=$_GET['user']??false;
    $this->font=__DIR__.'/views.ttf';
    $this->color=$this->colors($_GET['color']??'');
    $this->monitor=isset($_GET['monitor']);
    /* user if match */
    $ptrn='/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}$/i';
    if($this->user&&preg_match($ptrn,$this->user)){
      $fetch=$this->fetch('GET',$this->user);
      if($fetch){
        $this->fetch('PATCH',$this->user,[
          'visitor'=>intval($fetch->visitor)+1
        ]);
        $image=$this->getSVG($fetch->visitor);
        return $this->outSVG($image);
      }elseif(isset($_GET['register'])
        &&$this->register($_GET['register'])){
        $this->fetch('PUT',$this->user,[
          'visitor'=>1
        ]);
        $image=$this->getSVG('1');
        return $this->outSVG($image);
      }
    }
    /* build */
    elseif(isset($_GET['build'])){
      return $this->build($_GET['build']);
    }
    /* registered users */
    elseif(isset($_GET['rusers'])){
      $get=$this->fetch();
      return $this->result($get);
    }
    /* error output */
    return $this->error();
  }
  /* get image */
  protected function getSVG(string $int){
    $url='https://img.shields.io/badge/visitor-'.$int.'-orange.svg?style=for-the-badge';
    $get=file_get_contents($url);
    return $get;
  }
  /* output of image */
  protected function outSVG(string $svg){
    if(headers_sent()){exit;}
    $fname=$this->user.'.svg';
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Cache-Control: public, max-age=1');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="'.$fname.'"'); // attachment or inline
    header('HTTP/1.1 200 OK');
    header('Content-Type: image/svg+xml');
    header('Content-Length: '.strlen($svg));
    exit($svg);
  }
  /* result as json */
  protected function result($data=null){
    $json=@json_encode($data,JSON_PRETTY_PRINT);
    if(!headers_sent()){
      header('HTTP/1.1 200 OK');
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($json));
    }
    exit($json);
  }
  /* create image -- @return: GdImage resource */
  protected function create(int $count){
    $text=$this->user.': '.(string)$count.' views';
    $font=$this->font;
    $d=imagecreatefromstring($this->sample());
    $red=$this->color->red;
    $yellow=$this->color->yellow;
    $blue=$this->color->blue;
    $w=4+ceil(strlen($text)*12.8);
    $h=24;
    $size=16;
    $angle=0;
    $x=2;
    $y=18;
    $dpi=96;
    $quality=80;
    $n=imagecreatetruecolor($w,$h);
    imagecolortransparent($n,imagecolorallocatealpha($n,0,0,0,127));
    imagealphablending($n,false);
    imagesavealpha($n,true);
    imagecopyresampled($n,$d,0,0,0,0,$w,$h,1,1);
    $color=imagecolorallocate($n,$red,$yellow,$blue);
    $write=imagettftext($n,$size,$angle,$x,$y,$color,$font,$text);
    imageresolution($n,$dpi);
    return $n;
  }
  /* output of image */
  protected function out(GdImage $image){
    if(headers_sent()){exit;}
    $fname=$this->user.'.png';
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Cache-Control: public, max-age=1');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="'.$fname.'"'); // attachment or inline
    header('HTTP/1.1 200 OK');
    header('Content-Type: image/png');
    ob_start();
    imagepng($image);
    $byteLength=ob_get_length();
    $imageData=ob_get_clean(); 
    header('Content-Length: '.$byteLength);
    exit($imageData);
  }
  /* error output */
  protected function error(){
    header('Content-Type: image/jpeg');
    header('Content-Length: '.strlen($this->sample()));
    header('HTTP/1.1 200 OK');
    exit($this->sample());
  }
  /* fetch */
  protected function fetch(string $method='GET',string $path='',$data=null){
    $base='https://viewsx-default-rtdb.asia-southeast1.firebasedatabase.app/views/'.$path.'.json';
    $context=self::context($data,$method);
    $get=file_get_contents($base,false,$context);
    return $get?json_decode($get):$get;
  }
  /* register */
  protected function register(string $reg){
    $ptrn='#^github\.com/([^/]+)/([^/]+)/tree/([^/]+)$#';
    if(!preg_match($ptrn,$reg,$akur)){
      return false;
    }
    $name='views.bin';
    $host='https://raw.githubusercontent.com';
    $path="{$akur[1]}/{$akur[2]}/{$akur[3]}";
    $file="{$host}/{$path}/{$name}";
    $get=@file_get_contents($file);
    if($get==$this->sign($path)){
      return true;
    }return false;
  }
  /* build the sign file -- views.bin */
  protected function build(string $reg){
    $build=$this->sign($reg);
    if($build){
      header('Content-Description: File Transfer');
      header('Content-Disposition: attachment; '
        .'filename="views.bin"');
      header('Content-Type: application/octet-stream');
    }else{
      header('Content-Type: text/plain');
      $build='false';
    }
    header('Content-Length: '.strlen($build));
    header('HTTP/1.1 200 OK');
    exit($build);
  }
  /* sign */
  protected function sign(string $reg){
    $ptrn='#^([^/]+)/([^/]+)/([^/]+)$#';
    if(!preg_match($ptrn,$reg,$akur)){
      return false;
    }
    $raw=gzdeflate(md5($reg,true));
    $def=gzdeflate(implode($raw,$akur));
    return $def;
  }
  /* colors */
  protected function colors(string $color){
    $def=(object)[
      'red'=>0,
      'yellow'=>0,
      'blue'=>0,
    ];
    $ptrn='/^(\d{1,3}),(\d{1,3}),(\d{1,3})$/';
    if(!preg_match($ptrn,$color,$akur)){
      return $def;
    }
    return (object)[
      'red'=>min($akur[1],0xff),
      'yellow'=>min($akur[2],0xff),
      'blue'=>min($akur[3],0xff),
    ];
  }
  /* sample string image */
  protected function sample(){
    $head='data:image/png;base64,';
    $base='iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAC0lEQVQI12NgAAIAAAUAAeImBZsAAAAASUVORK5CYII=';
    return base64_decode($base);
  }
  /* context
   * ~ context for fopen
   * @parameter:
   *   $data    = array of post data
   *   $cookie  = string of cookie; default: ''
   *   $method  = string of method; default: GET
   *   $header  = array of header; default: null
   *   $ua      = string of user agent; default: 9r3i
   * @user-agent: mobile
   *   Opera/9.80 (Android; Opera Mini/43.2.2254/146.51; U; en) Presto/2.12.423 Version/12.16
   * @return: resource of stream context
   * @usage:
   *   $o=fopen('https://domain.com/','rb',false,views::context($data,$cookie,$method,$header,$ua));
   */
  final protected static function context($data=[],$method='GET',$header=[],$cookie='',$ua=null){
    /* default methods */
    $methods=['GET','POST','OPTIONS','PUT','PATCH','DELETE'];
    /* data query for post */
    $data=is_array($data)?$data:[];
    $content=http_build_query($data);
    $content=json_encode($data);
    /* default headers */
    $headers='Content-Type: application/x-www-form-urlencoded;charset=utf-8;'."\r\n";
    $headers='Content-Type: application/json;'."\r\n";
    $headers.='Content-Length: '.strlen($content);
    /* set user-agent */
    $om='Opera/9.80 (Android; Opera Mini/43.2.2254/146.51; U; en) Presto/2.12.423 Version/12.16';
    $ua=is_string($ua)?'User-Agent: '.$ua
      :'User-Agent: Mozilla/5.0 (Windows NT 11; x128; rv:93; 9r3i) Gecko/20100101 Firefox/93';
    /* prepare other headers and cookie */
    $header=is_array($header)?implode("\r\n",$header):'';
    $cookie=is_string($cookie)?$cookie:'';
    $headers.="\r\n".$ua."\r\ncookie:".$cookie."\r\n".$header."\r\n";
    /* set the method */
    $method=is_string($method)&&in_array($method,$methods)?$method:'GET';
    /* return stream context */
    return @stream_context_create([
      'http'=>[
        'header'=>$headers,
        'method'=>$method,
        'content'=>$content,
      ],
      'ssl'=>[
        'cafile'=>__DIR__.'/cacert.pem',
        'verify_peer'=>false,
        'verify_peer_name'=>false,
        /* capture_session_meta is deprecated - read: stream_get_meta_data */
        //'capture_session_meta'=>true,
        //'crypto_method'=>STREAM_CRYPTO_METHOD_TLS_CLIENT,
        'crypto_method'=>STREAM_CRYPTO_METHOD_ANY_CLIENT,
      ]
    ]);
  }
});


