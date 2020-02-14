# Kontejnery 
## pg-api
pro produkční aplikaci, obsahuje zdrojový kód, je v něm server, chybí dev věci a composer.
## pg-api-template
pro vytvoření prodkukčního kontejneru
## pg-api-build
pro vytvoření produkčního kontejneru. Běží v něm testy, stahují se knihovny composerem. Není v něm server.
## pg-api-dev
slouží pro vývoj. Obsahuje vše potřebné pro vývoj: composer, dev věci, server ...

#LoginPresenter

##methods
 
### login
{ "id_token": "XYZ123"}

value from GoogleLogin.  
id_token validator: https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=XYZ123

