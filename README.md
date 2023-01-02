# views
An image generator for viewers

# Sample
[![9r3i Visitors](https://9r3i.web.id/api/views/?user=9r3i-views&color=51,119,187&register=github.com/9r3i/views/tree/master)](https://github.com/9r3i)

# Usage
Basic sample
```
https://9r3i.web.id/api/views/?user=9r3i-views
```

## Hosts
Right now, available hosts only in
```
https://9r3i.web.id
```
And path to API is ```/api/views/```

## Parameters
- Require parameter is ```user```, is user-key to views counter
- Next is parameter ```register```, read more about it in [Register](#register) section
- The last one is parameter ```color```, this is optional, default value is ```0,0,0``` of red, yellow and blue, separated by comma, see the sample below [Add Text Color](#add-text-color)

## Register
First user cannot access the API, so it's gonna need some register, add to url query with key ```register``` with value a github repository that has views binary file ```views.bin```, for example:
```
https://9r3i.web.id/api/views/?user=9r3i&register=github.com/9r3i/views/tree/master
```

## Add Text Color
Add color soft red
```
https://9r3i.web.id/api/views/?user=9r3i&color=187,51,51
```

# Closing
That's all there is to it. Alhamdulillaah...

