
[![Author](https://img.shields.io/badge/author-9r3i-lightgrey.svg)](https://github.com/9r3i)
[![License](https://img.shields.io/github/license/9r3i/views.svg)](https://github.com/9r3i/views/blob/master/LICENSE)
[![Forks](https://img.shields.io/github/forks/9r3i/views.svg)](https://github.com/9r3i/views/network)
[![Stars](https://img.shields.io/github/stars/9r3i/views.svg)](https://github.com/9r3i/views/stargazers)
[![Issues](https://img.shields.io/github/issues/9r3i/views.svg)](https://github.com/9r3i/views/issues)
[![Release](https://img.shields.io/github/release/9r3i/views.svg)](https://github.com/9r3i/views/releases)
[![Donate](https://img.shields.io/badge/donate-paypal-orange.svg)](https://paypal.me/9r3i)


# views
An image generator for viewers

# Usage
Basic sample
```
https://sabunjelly.com/api/views/?user=9r3i-views
```

## Hosts
Right now, available hosts only in
```
https://sabunjelly.com
```
And path to API is ```/api/views/```

## Parameters
There are ```user``` (required), ```register```, ```color``` and ```monitor```.

### user
This parameter is required, as user-key to views counter, for example: 
```
https://sabunjelly.com/api/views/?user=9r3i-views
```

### register
First access user cannot use the API, so it's gonna need some register, add parameter ```register``` to url query with value a github repository that has views binary file ```views.bin```, for example:
```
https://sabunjelly.com/api/views/?user=9r3i-views&register=github.com/9r3i/views/tree/master
```

### color
This parameter is to add text color, this parameter ```color``` is optional, default value is ```0,0,0``` (black), the values of red, yellow and blue, separated by comma, for example to add color soft blue:
```
https://sabunjelly.com/api/views/?user=9r3i-views&color=51,119,187
```
The result will be like this:
[![9r3i Visitors](https://sabunjelly.com/api/views/?user=9r3i-views&color=51,119,187)](https://github.com/9r3i/views)

### monitor
This parameter is for monitoring the views, if this parameter is set, the counter won't increase, for example:
```
https://sabunjelly.com/api/views/?user=9r3i-views&monitor=true
```

# Closing
That's all there is to it. Alhamdulillaah...

