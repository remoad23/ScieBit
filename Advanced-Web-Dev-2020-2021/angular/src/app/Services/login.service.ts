import {Injectable, OnInit} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute} from "@angular/router";

@Injectable({
  providedIn: 'root'
})
export class LoginService{

  private username: string = "";
  private valid: boolean;
  private subscription;
  private initSub;
  public token: string;
  public id: number;
  public share: string;

  constructor(private route: ActivatedRoute,private http: HttpClient) {

  }

  /**
   * Check if token is valid for the user with specific id
   * @param token
   */
  public isLogin() : boolean
  {
    let isLoggedIn;

    if(this.valid){
      isLoggedIn = this.reCheck()
    }
    return isLoggedIn;
  }

  public init() : boolean
  {
    let valid = true;
    this.initSub = this.route.queryParams.subscribe(params => {
      [this.id,this.token,this.share] = [params.id,params.token,params.shared ?? "NotFound"];

      // leave init,if url query params are null
      if(!params.id || !params.token) return;
      //unsubscribe after query params have been found after page load
      if(params.id && params.token ) this.initSub.unsubscribe();

      this.subscription = this.http.get<any>(`http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/currentuser/${this.id}/${this.token}`)
        .subscribe(
          data => {
            [this.username,this.valid] = [data.username,data.valid];
            (document.querySelector('header > p') as HTMLParagraphElement).innerText = this.username;
            this.subscription.unsubscribe();
          },
          error =>  valid = false ,
        );
    });
    return valid;
  }

  /**
   * Check if User is still authenticated and still the same user
   */
  private reCheck() : boolean
  {
    let valid = true;
    if(!this.id || !this.token) return false;
    this.subscription = this.http.get<any>(`http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/currentuser/${this.id}/${this.token}`)
      .subscribe(
        data => valid = this.username == data.username ? true : false,
      );
    return valid;
  }

  public getUserType(userlock: {userType: string})
  {
    this.subscription = this.http.get<any>(`http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/usertype/${this.id}/${this.token}`)
      .subscribe(data => {userlock.userType = data});
  }

}
