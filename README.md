# Routex

Routex is my attempt at moving [Limonade-php](https://github.com/sofadesign/limonade) forward as library. I admire what Fabrice has created, but I feel that with the new functionality offered by PHP(namespaces, better oop) it is time to move this great library forward. 

If you've been through limonade-php source, you'll notice very little of that actually survived in Routex. In fact, the whole system looks completely foreign - yet I assure you, it is based on limonade-php. The routing mechanics (for example) are ported almost exactly. However, your old limonade-php code will NOT work with Routex, hence the different name. 


## Features
- Complex routing (with variables) - Define /api/:version/action/:format as a valid URL
- Route injection - The ability to load MORE routes and have them parsed against the request on the fly. 


## Todo
- Hooks - Yay. Hooks will allow anyone to expand Routex with code as they need.

