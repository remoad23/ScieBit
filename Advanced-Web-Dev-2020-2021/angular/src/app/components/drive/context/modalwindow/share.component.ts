import {Component, ElementRef, Input, ViewChild, ViewContainerRef, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";
import {LoginService} from "../../../../Services/login.service";
import {SharedService} from "../../../../Services/shared.service";

@Component({
  selector: 'share',
  template: `
    <div #shareModal class="modalWindowWrapper">
      <div class="modalWindow">
        <h1 i18n>Share File</h1>
        <label i18n>Email</label>
        <div class="inputContainer">
          <select *ngIf="userLock?.userType === 'Admin'" #selectedType>
            <option selected value="1">User</option>
            <option value="2">Admin</option>
          </select>
          <input #searchbar i18n-placeholder placeholder="examplemail@..." class="inputSearchUser" type="text" [value]="currentText" (change)="newResult = false" (input)="queryUser($event.target.value)">
          <div *ngIf="!closed && queriedUser[0] != 'NotFound'"   class="queryUserWrapper">
            <div (click)="getUserNameOfTag(user.email,user.id)" class="queryUser" *ngFor="let user of queriedUser[0]">
              {{user.email}}
            </div>
          </div>
        </div>
        <div class="buttonVersioningWrapper">
          <button class="buttonSuccess" #submitBtn (click)="shareFileSubmit()" disabled i18n>Share</button>
          <button class="buttonCancel" (click)="cancel()" i18n>Cancel</button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .modalWindow
    {
      position: absolute;
      width: 25vw;
      background-color: white;
      left: 38%;
      top: 25%;
      display: flex;
      flex-direction: column;
      padding: 35px;
      border-radius: 15px;
      height: auto !important;
      color: #36506C;
    }

    .modalWindowWrapper
    {
      width: 100vw;
      position: fixed;
      height: 100vh;
      background-color: rgba(0,0,0,0.8);
      left: 0;
      top: 0;
      z-index: 1;
    }
    button
    {
      width: 32%;
      height: 2vw;
    }
    .buttonVersioningWrapper{
      display: flex;
      flex-direction: row;
      width: 100%;
      justify-content: space-evenly;
      margin-top: 20px;
    }
    .inputContainer
    {
      margin-top: 5px;
    }
    .queryUser
    {
      border:1px solid #36506C;
      height: 1.4vw;
      display: flex;
      align-items: center;
      color: #233142;
    }
    .queryUser:hover,.showMoreUser:hover
    {
      cursor: pointer;
      background-color:#D9E0E2;
    }
    .queryUserWrapper
    {
      position: absolute;
      border: 1px solid #36506C;
      background-color: #EBF7FD;
      width: 25.3vw;
      max-height: 10vw;
      overflow-y: scroll;
      overflow-x: hidden;
    }
    .inputSearchUser
    {
      width: 25vw;
      height: 1.5vw;
      margin-top: 10px;
    }

    @media (max-width: 1270px) {
      .modalWindow
      {
        width: 40vw;
        left: 30%;
      }
      button
      {
        height: 3vw;
      }

      .inputSearchUser {
        width: 40vw;
        height: 2vw;
      }

      .queryUserWrapper {
        width: 40.4vw;
        max-height: 20vw;
      }

      .queryUser
      {
        height: 3vw;
      }
    }

    @media (max-width: 800px) {
      .modalWindow {
        width: 65vw;
        left: 9%;
      }

      button {
        width: 35%;
        height: 7vw;
      }

      .inputSearchUser {
        width: 64vw;
        height: 5vw;
      }

      .queryUserWrapper {
        width: 65.1vw;
        max-height: 40vw;
      }

      .queryUser
      {
        height: 6vw;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class ShareComponent
{

  queriedUser;
  selectedUser;
  newResult: boolean;
  currentText: string;
  closed: boolean;
  // id of the file the component belongs to
  fileID: number;
  userLock;
  @ViewChild('selectedType',{read: ElementRef}) selectedUserType;
  @ViewChild('submitBtn') subbtn: ElementRef;
  @ViewChild('shareModal',{read: ViewContainerRef}) shareModal;

  constructor(private drive: DriveService,private router: Router,private login: LoginService,private share: SharedService) {
    this.queriedUser = [];
    this.newResult = false;
    this.currentText = "";
    this.closed = true;
    this.selectedUser = {};
    this.userLock = {userType: "notFound"};
    this.login.getUserType(this.userLock);
    if(this.share.getObject("mousemenu") !== "NotFound")
    this.share.getObject('mousemenu')?.hideWhenInterActionClicked();
  }


  shareFileSubmit()
  {
    if(this.selectedUserType?.nativeElement?.value == 2)
    {
      this.drive.makeSharedFile(this.selectedUser.id,this.fileID,"Admin");
    }
    else{
      this.drive.makeSharedFile(this.selectedUser.id,this.fileID,"User");
    }

    this.cancel();
  }

  queryUser(input:string)
  {
    this.newResult = input === '' ? false : true;
    this.closed =  input === '' ? true : false;
    if(!this.newResult) return;
    let url
    if(this.router.url.startsWith("/my"))
    {
      url = "mydocument";
      this.drive.getUserByHttp(url,
        this.queriedUser,
        input,
        this.selectedUserType?.nativeElement?.value || 1,
        this.subbtn);
    }
    else if(this.router.url.startsWith("/department"))
    {
      url = "department";
      this.drive.getUserByHttp(url,
        this.queriedUser,input,
        this.selectedUserType?.nativeElement?.value || 1,
        this.subbtn);
    }

  }

  private getUserNameOfTag(tagValue,id)
  {
    this.selectedUser = {id: id,email: tagValue};
    this.currentText = tagValue;
    this.closed = true;
  }


  /**
   * get out of version modal window without saving
   */
  cancel()
  {
    if(this.share.getObject("mousemenu") !== "NotFound" && this.share.getObject("input") !== "NotFound")
    {
      this.share.getObject("mousemenu").modalWindowInitiated = false;
      this.share.getObject("mousemenu").input.remove(0);
      this.share.getObject("input").clear();
      this.share.removeObject("input");
    }
    else {
      //make object destroy itself
      this.shareModal
        .element
        .nativeElement
        .parentElement
        .removeChild(this.shareModal.element.nativeElement);
    }
  }

}
