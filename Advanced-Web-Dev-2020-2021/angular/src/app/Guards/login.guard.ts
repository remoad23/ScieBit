import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import {LoginService} from "../Services/login.service";

@Injectable({
  providedIn: 'root'
})
export class LoginGuard implements CanActivate {

  private loginService: LoginService

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean | UrlTree {
    if(this.loginService.isLogin())
    {
        return true;
    }
    else {
      window.location.href = "http://localhost/Advanced-Web-Dev-2020-2021/public/"
    }
    return false;
  }

  constructor(private _loginService: LoginService) {
    this.loginService = _loginService;
  }

}
