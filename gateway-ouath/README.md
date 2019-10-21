# ORM Template and Generator


### Main concepts in implementation

- [GateWay](#generation-from-db)
    - [Redis Cache](#references)
    - [MicroService Communication](#references)
    - [LogStorage](#references)
- [Oauth-2 implementation](#available-methods)


---

## General Purpose
The main purpose is that create a light weight framework for microservice implementation in PHP.
This core part is handle all requests through on gateway and ouath-2 library was settled on middleware. All requests are have to pass from gateway route of the gateway have been prepared for control authentication of the user.

Not only user token but also some required user information is saved in session to prevent re-invoke of the user-service.
Also redis cache have been used for all requstes also in gateway, based on STATUSCODE of the responses are cleared, if any update/create/delete etc.. methods are called with 201 or 202 statuscode numbers.


![Project Image](https://i.ibb.co/LtfQKZP/Gateway-Implementation-Yigit.png)

for implementation of php microservice you may check:
--url