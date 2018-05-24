<?

    $user->changeCity($this->GET[0]);
    $http_referer = $this->getAddress($_SERVER['HTTP_REFERER']);

    $subdomain = explode('.', $_SERVER['HTTP_HOST']);
    if( count($subdomain) > 2 ){
        $domain = $subdomain[1].'.'.$subdomain[2];
    }else{
        $domain = $subdomain[0].'.'.$subdomain[1];
    }

    $_SESSION['city']['name'] = str_replace(' ', '-', $_SESSION['city']['name']);
    header('Location: http://'.$_SESSION['city']['name'].'.'.$domain.'/'.$http_referer);
    exit;

?>