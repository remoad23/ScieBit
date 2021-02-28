import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {Observer} from "rxjs";
import {LoginService} from "./login.service";
import {FileComponent} from "../components/drive/assets/file/file.component";
import {MydocumentComponent} from "../components/drive/tables/my/mydocument.component";
import {DepartmentComponent} from "../components/drive/tables/department/departmentdocument.component";
import {ShareddocumentComponent} from "../components/drive/tables/shared/shareddocument.component";
import {Router} from "@angular/router";
import {SharedService} from "./shared.service";

@Injectable({
  providedIn: 'root'
})
export class DriveService {


  private http;
  private readonly subURL: any;
  private defaultURL: string = "http://localhost:80/Advanced-Web-Dev-2020-2021/public/drive/request/";
  private dataObserver : any;
  private login: LoginService;


  constructor(private _http: HttpClient,
              private loginService: LoginService,
              private router: Router,
              private shared: SharedService)
  {
    this.subURL =  {
      mydocument: "mydocument",
      mydocumentfiles: "mydocumentfiles",
      shareddocument: "shareddocument",
      shareddocumentfiles: "shareddocumentfiles",
      departmentdocument: "departmentdocument",
      finance: "departmentdocument/finance",
      controlling: "departmentdocument/controlling",
      development: "departmentdocument/development",
      marketing: "departmentdocument/marketing",
      humanresources: "departmentdocument/humanresources",
      financefiles: "departmentdocument/finance/files",
      controllingfiles: "departmentdocument/controlling/files",
      developmentfiles: "departmentdocument/development/files",
      marketingfiles: "departmentdocument/marketing/files",
      humanresourcesfiles: "departmentdocument/humanresources/files",
      mydocumentfolder: "mydocument/folder",
      shareddocumentfolder: "shareddocument/folder",
      departmentdocumentfolder: "departmentdocument/folder",
      folder: "folder",
      file: "file",
      allfiles: "allfiles"
    };
    this.http = _http;
    this.login = loginService;
  }

  /**
   * Get all files of a section
   * @param url
   * @param _data
   */
  public getDataByHttp(url: string = "",_data: any )
  {
    // kill http request if no userid has been found
    if(!this.login.id) {
      return;
    }

     this.dataObserver = this._http.get<any>(this.defaultURL + this.subURL[url] + "/" + this.login.id + "/" + this.login.token )
       .subscribe(
       data => {
         let tempArray = [];
         if(data[0].length <= 0){
           _data = [];
           _data.push([]);
           return;
         }
         for(let singleData of data[0])
         {
           if(tempArray.length-1 === 6)
           {
             tempArray.push(singleData);
             _data.push([...tempArray]);
             tempArray = [];
           }
           /*// put the rest of object in the last array that cant fill a complete array with 7 objects
           else if(data[0][data[0].length-1].id === singleData.id)
           {
             tempArray.push(singleData);
             _data.push([...tempArray]);
           }*/
           else
           {
             tempArray.push(singleData);
           }
         }
         if(tempArray.length > 0){
           _data.push([...tempArray]);
         }
       },
        err => { console.error(err); },
       )

  }

  /**
   * Get all files of a section
   * @param url
   * @param _data
   */
  public getDepartmentFolder(_data: any,$folderid )
  {
    // kill http request if no userid has been found
    if(!this.login.id) {
      return;
    }

    this.dataObserver = this._http.get<any>(`${this.defaultURL}departmentdocument/folder/${this.login.id}/${this.login.token}/${$folderid}`)
      .subscribe(
        data => {
          let tempArray = [];
          if(!data[0]) return;
          for(let singleData of data[0])
          {
            if(tempArray.length-1 === 6)
            {
              tempArray.push(singleData);
              _data.push([...tempArray]);
              tempArray = [];
            }
            // put the rest of object in the last array that cant fill a complete array with 7 objects
            else if(data[0][data[0].length-1].id === singleData.id)
            {
              tempArray.push(singleData);
              _data.push([...tempArray]);
            }
            else
            {
              tempArray.push(singleData);
            }
          }
        },
        err => { console.error(err); },
      )

  }

  /** return a json object to get the new file from the DB
   * make a new File and upload it
   * @param url
   * @param file
   *
   */
  public makeFile(file: any,done: {uploaded: boolean},fileToPushTo:any, keywords:any,isInsideFolder: boolean)
  {
    let formData = new FormData();
    formData.append('fileUpload', file, file.name);
    formData.append('keywords', JSON.stringify(keywords));
    if(isInsideFolder)
    formData.append('parentFolderId',this.shared.getObject('currentFolderContent').id);

    let url = "";
    if(this.router.url === "/department")
    {
      let department = this.shared.getObject("departmentdocument");
      let departmentid = department.departmentSelection.nativeElement.value;
      url = this.defaultURL + "departmentdocument" + "/" + departmentid + "/"+ this.login.id + "/" + this.login.token + "/upload/file";
    }
    else {
      url = this.defaultURL + this.subURL["mydocument"] + "/" + this.login.id + "/" + this.login.token + "/upload/file";
    }

    this._http.post<any>(url ,
      formData,
      )
      .subscribe(
        data => { Object.assign(fileToPushTo,data); },
        err => { console.error(err); },
        () => {done.uploaded = true;},
      );
  }

  /**
   * Make a sharedFile when a user wants to share something with another user
   * @param file
   * @param done
   * @param fileToPushTo
   */
  public makeSharedFile(id:number,fileId:number,requesterType)
  {
    let formData = new FormData();
    formData.append('userType',requesterType);

    this._http.post<any>( this.defaultURL + this.subURL["mydocument"] + "/" + this.login.id + "/" + this.login.token + "/upload/sharedfile/" + id + "/" + fileId,formData)
      .subscribe(
        data => {  },
        err => { console.error(err); },
        () => {},
      );
  }

  public makeFolder(folder: any,done: {uploaded: boolean},folderToPushTo:any,isInsideFolder: boolean)
  {
    let formData = new FormData();
    for(let file of folder)
    {
      formData.append('folderUpload[]',file,file.name);

      let parentFolder = file.webkitRelativePath;
      let slashIndex = file.webkitRelativePath.lastIndexOf('/');

      let folderArray = [
        this.getParentPath({path: parentFolder}),
        file.webkitRelativePath,
        file.webkitRelativePath.slice(0,slashIndex)];

      let folderParentArray = [
        this.getParentPath({path: parentFolder}),
        file.webkitRelativePath.slice(0,slashIndex)];

      formData.append('folderPath[]',JSON.stringify(folderArray));
      formData.append('folderParentPath[]',JSON.stringify(folderParentArray));
    }

    if(isInsideFolder)
      formData.append('parentFolderId',this.shared.getObject('currentFolderContent').id);

    let url = "";
    if(this.router.url === "/department")
    {
      let department = this.shared.getObject("departmentdocument");
      let departmentid = department.departmentSelection.nativeElement.value;
      url = this.defaultURL + "departmentdocument" + "/" + departmentid + "/" + this.login.id + "/" + this.login.token + "/upload/folder";
    }
    else {
      url = this.defaultURL + this.subURL["mydocument"] + "/" + this.login.id + "/" + this.login.token + "/upload/folder";
    }


    this._http.post<any>(url, formData)
      .subscribe(
        folderData => {Object.assign(folderToPushTo,folderData)},
        err => { console.error(err); },
        () => {done.uploaded = true;},
      );
  }

  public makeSharedFolder(id:number,folderId:number,requesterType: string,done: {uploaded: boolean})
  {
    let formData = new FormData();

    formData.append('userType',requesterType);

    this._http.post<any>(
      this.defaultURL + this.subURL["mydocument"] + `/${this.login.id}/${this.login.token}/upload/sharedfolder/${id}/${folderId}`,
      formData)
      .subscribe(
        data => { },
        err => { console.error(err); },
        () => {done.uploaded = true;},
      );
  }


  /**
   * Get all childfiles and childfolders of a folder
   * @param url
   * @param _data
   */
  public getChildData(url: string = "",_data: any,folderID )
  {
    // kill http request if no userid has been found
    if(!this.login.id) {

      return;
    }

    this.dataObserver = this._http.get<any>(this.defaultURL + "folder/childdata/" + this.subURL[url]
      + "/" + this.login.id + "/" + this.login.token +"/"+ folderID )
      .subscribe(
        data => {
          let tempArray = [];
          if(!data[0]) return;
          for(let singleData of data[0])
          {
            if(tempArray.length-1 === 6)
            {
              tempArray.push(singleData);
              _data.push([...tempArray]);
              tempArray = [];
            }
            // put the rest of object in the last array that cant fill a complete array with 7 objects
            else if(data[0][data[0].length-1].id === singleData.id)
            {
              tempArray.push(singleData);
              _data.push([...tempArray]);
            }
            else
            {
              tempArray.push(singleData);
            }
          }
        },
        err => { console.error(err); },
      )

  }

  determineDepartments(currentDepartments)
  {
    this._http.get<any>( this.defaultURL + "department/" + this.login.id + "/" + this.login.token + "/get")
      .subscribe(
        data => {
          if(!data) return;
          // decide which department should be added to <select> html element as <option>
          if(Object.values(data).includes(1))
            currentDepartments.push( { finance: "departmentdocument/finance",id: 1} );
          if(Object.values(data).includes(2))
            currentDepartments.push( { controlling: "departmentdocument/controlling",id: 2} );
          if(Object.values(data).includes(3))
            currentDepartments.push( { development: "departmentdocument/development",id: 3} );
          if(Object.values(data).includes(4))
            currentDepartments.push( { marketing: "departmentdocument/marketing",id: 4} );
          if(Object.values(data).includes(5))
            currentDepartments.push( { humanresources: "departmentdocument/humanresources",id: 5} );
        },
        err => { console.error(err); },
      );
  }

  /**
   * Delete Content from DB
   * @param url the requested url
   * @param _data
   */
  deleteDataByHttp(url: string = "",passData,dataID,done)
  {
    // kill http request if no userid has been found
    if(!this.login.id) {
      return;
    }

    //zu delete noch ändern
    this._http.get<any>(this.defaultURL + this.subURL[url] + "/" + this.login.id + "/" + this.login.token +"/delete/" + dataID)
      .subscribe(data =>{
        passData = data;},
          err => { console.error(err); },
        () => {done.uploaded = true;},);

  }

  /**
   * Delete Content from DB
   * @param url the requested url
   * @param _data
   */
  deleteFolder(url: string = "",passData,dataID,done)
  {
    // kill http request if no userid has been found
    if(!this.login.id) {
      return;
    }

    //zu delete noch ändern
    this._http.get<any>(this.defaultURL + this.subURL[url] + "/" + this.login.id + "/" + this.login.token +"/delete/folder/" + dataID)
      .subscribe(data =>{
          passData = data;},
        err => { console.error(err); },
        () => {done.uploaded = true;},);

  }

  /**
   * Delete Content from DB
   * @param url the requested url
   * @param _data
   */
  getUserByHttp(url: string = "",passData,query:string,userType,sucessBtn)
  {
    // kill http request if no userid has been found
    if(!this.login.id) {
      return;
    }

    this._http.get<any>(this.defaultURL + this.subURL[url] + `/${this.login.id}/${this.login.token}/${query}/${userType}`)
      .subscribe(
        data =>{ if(!data) {
              let empty = [];
              empty.push([])
              Object.assign(passData,data);
              return;
            }
            Object.assign(passData,data);},
        err => { console.error(err);},
        () => { sucessBtn.nativeElement.disabled = false;}
          );

  }


  private getParentPath(parentPath: {path: string}) : string
  {
    if(parentPath.path.includes('/'))
    {
      let slashIndex = parentPath.path.lastIndexOf('/');
      parentPath.path = parentPath.path.slice(0,slashIndex);

      if(parentPath.path.includes('/')) {
        let slashIndex = parentPath.path.lastIndexOf('/');
        parentPath.path = parentPath.path.slice(slashIndex);
        return parentPath.path.slice(1);
      }
      else {
        return parentPath.path;
      }

    }
    else {
      return parentPath.path;
    }
  }

  /**
   * Set the file or folder to specific Version
   * @param selectedFileId the file version to switch
   * @param versionId the filegroup associated with the file and the other versions of the file
   * @param the file,which will have the property of the selectedFile
   */
  versionize(newFile,selectedFileId: number,versionId: number,done)
  {
    let formData = new FormData();
      formData.append('fileID',JSON.stringify(selectedFileId));
    formData.append('versionId',JSON.stringify(versionId));

    this._http.post<any>(`${this.defaultURL}${this.subURL["mydocument"]}/${this.login.id}/${this.login.token}/version/file/change`,
      formData)
      .subscribe(
        selectedFile => {Object.assign(newFile,selectedFile);},
        err => { console.error(err); },
        () => {done.uploaded = true;},
      );
  }

  /**
   * Set the file or folder to specific Version
   * @param selectedFileId the file version to switch
   * @param versionId the filegroup associated with the file and the other versions of the file
   * @param the file,which will have the property of the selectedFile
   */
  addNewFileVersion(file,newFile,selectedFileId: number,versionId: number,done,keywords:any)
  {
    let formData = new FormData();
    formData.append('fileID',JSON.stringify(selectedFileId));
    formData.append('versionId',JSON.stringify(versionId));
    formData.append('fileUpload', file, file.name);
    formData.append('keywords', JSON.stringify(keywords));

    this._http.post<any>(`${this.defaultURL}${this.subURL["mydocument"]}/${this.login.id}/${this.login.token}/version/file/create`,
      formData)
      .subscribe(
        selectedFile => {Object.assign(newFile,selectedFile);},
        err => { console.error(err); },
        () => {done.uploaded = true;},
      );
  }

  /**
   * get the file or folder to specific Version
   * @param selectedFileId the file version to switch
   * @param versionId the filegroup associated with the file and the other versions of the file
   * @param the file,which will have the property of the selectedFile
   */
  getVersion(data,versionId: number)
  {
    this._http.get<any>(`${this.defaultURL}${this.subURL["mydocument"]}/${this.login.id}/${this.login.token}/${versionId}/version/file`,)
      .subscribe(
        files => {Object.assign(data,files);},
        err => { console.error(err); },
      );
  }

  /**
   * Moves a Folder/File to another Folder after it has been dragged and dropped into a folder
   */
  moveTo(changed,folderToPutInsideID:number,fileID:number= 0,folderID:number= 0)
  {
    let url = "";
    let formData = new FormData();
    if(!folderToPutInsideID){
      folderToPutInsideID = null;
    }

    if(fileID)
    {
      formData.append('fileID',JSON.stringify(fileID));
      formData.append('folderIdToMoveIt',JSON.stringify(folderToPutInsideID));
      url = `${this.defaultURL}${this.login.id}/${this.login.token}/move/file`;
    }
    else if(folderID)
    {
      formData.append('folderID',JSON.stringify(folderID));
      formData.append('folderIdToMoveIt',JSON.stringify(folderToPutInsideID));
      url = `${this.defaultURL}${this.login.id}/${this.login.token}/move/folder`;
    }

    this._http.post<any>(url,formData).subscribe(
      data => {},
      err => { console.error(err); },
      () =>{ changed.uploaded = true;},
    );
  }

  /**
   * Updates the name and keywords of the given file
   */
  updateFile(fileId, done: {uploaded: boolean},filename, deletedKeywords: any, addedKeywords: any){
    let formData = new FormData();
    formData.append('fileID',JSON.stringify(fileId));
    formData.append('updatedFilename', filename);
    formData.append('deletedKeywords', JSON.stringify(deletedKeywords));
    formData.append('addedKeywords', JSON.stringify(addedKeywords));

    this._http.post<any>(`${this.defaultURL}${this.subURL["mydocument"]}/${this.login.id}/${this.login.token}/file/update`,
      formData)
      .subscribe(
        data => {},
        err => { console.error(err); },
        () => {done.uploaded = true;},
      );
  }

  /**
   * sorts the folders array by name
   * @param folders: array to sort
   */
  sortByNameFolders(folders){
    let tempArray = [];
    //folder sort
    for(let i = 0; i < folders.length; i++){
      for(let j = 0; j < folders[i].length; j++){
        tempArray.push(folders[i][j]);
      }
    }
    tempArray.sort((a, b) => a.foldername.localeCompare(b.foldername));
    for(let i = 0; i < folders.length; i++){
      for(let j = 0; j < folders[i].length; j++){
        folders[i][j] = tempArray[i * folders[0].length + j];
      }
    }
    return folders;
  }

  /**
   * sorts the files array by name
   * @param files: array to sort
   */
  sortByNameFiles(files){
    let tempArray = [];
    //file sort
    for(let i = 0; i < files.length; i++){
      for(let j = 0; j < files[i].length; j++){
        tempArray.push(files[i][j]);
      }
    }
    tempArray.sort((a, b) => a.filename.localeCompare(b.filename));
    for(let i = 0; i < files.length; i++){
      for(let j = 0; j < files[i].length; j++){
        files[i][j] = tempArray[i * files[0].length + j];
      }
    }
    return files;
  }

  /**
   * sorts an array by date
   * @param sortArray: array to sort
   */
  sortByDate(sortArray){
    let tempArray = [];
    for(let i = 0; i < sortArray.length; i++){
      for(let j = 0; j < sortArray[i].length; j++){
        tempArray.push(sortArray[i][j]);
      }
    }
    tempArray.sort(function (a, b)
    {
      let dateA = new Date(a.created_at);
      let dateB = new Date(b.created_at);
      return dateA.getTime() - dateB.getTime();
    });
    for(let i = 0; i < sortArray.length; i++){
      for(let j = 0; j < sortArray[i].length; j++){
        sortArray[i][j] = tempArray[i * sortArray[0].length + j];
      }
    }
    return sortArray;
  }

  getNotifications(pagination)
  {
    this._http.get<any>("http://localhost/Advanced-Web-Dev-2020-2021/public/drive/notification/"+this.login.id+"/"+this.login.token+"/"+pagination.index).subscribe(
      data => {
        let body = data;
        pagination.index++;
        if(body != null){
          if(body[0].stringHtml === "NoNotificationsFound" || body[0].stringHtml == null)
          {
            return;
          }
          else{
            let headerList = document.getElementById('headerNotificationList')

            let moreNotifications = document.getElementById('getMoreNotifcations');
            headerList.removeChild(moreNotifications);

            for(let i = 0; i < body[0].stringHtml.length; i++){
              let message = document.createElement('li');
              message.style.listStyleType = 'none';
              message.innerHTML = body[0].stringHtml[i];
              let messageDelete = message.lastChild;
              messageDelete.addEventListener('click',()=>{
                this.deleteNotification(body[0].idArray[i], message)
              });
              headerList.appendChild(message);
            }

            headerList.append(moreNotifications);
            this.checkForNotificationButton(headerList);
          }
        }
      },
      err => { console.error(err); },
      () =>{ },
    );
  }

  /**
   * get the path to click on in the drive navigation bar
   * @param $folderid the id of the folder to trace the root folder
   * @param $folderRoute the array to put the paths inside to show in the view
   */
  getFolderRoute(folderid,folderRoute,section)
  {

    // if true then other request sent to get sharedpath
    let addSectionToUrl = section === "/shared" ? "shared/" : "";


    let url = `${section}/folder/`
    this._http.get<any>(`http://localhost/Advanced-Web-Dev-2020-2021/public/drive/path/${addSectionToUrl}${this.login.id}/${this.login.token}/${folderid}`)
      .subscribe(
        data => {
          data.reverse();
            for(let folder of data)
            {
              folderRoute.push({ url:url+folder[0],name:folder[1] });
            }
          },
        err => { console.error(err); },
      );
  }

  /**
   * deletes one element from the files matrix
   * @param files: files matrix
   * @param id: id of the element that will be deleted
   */
  deleteOneFromFiles(files, id){
    let found = false;

    for(let x = 0; x <= files.length-1; x++) {

      for (let y = 0; y <= files[x].length - 1; y++) {
        let innerFile = files[x][y];
        // if file has been found then delete it and filter row to get rid of empty index
        if (innerFile.id === id) {
          delete files[x][y];
          files[x] = files[x].filter(n => n !== (undefined || null));
          found = true;
          break;
        }
        // Make sure the deleted space will be filled with columns from next rows to make rows even
        if (found) {
          let previousRow = files[x - 1];
          let currentRowColumn = files[x][y];
          previousRow.push(Object.assign(currentRowColumn));
          delete files[x][y];
          files[x] = files[x].filter(n => n !== (undefined || null));
          break;
        }
      }
    }
    return files;
  }

  /**
   * deletes the message
   */
  deleteNotification(id, element){
    this._http.get<any>("http://localhost/Advanced-Web-Dev-2020-2021/public/drive/notification/"+this.login.id+"/"+this.login.token+"/"+id+"/delete").subscribe(
      data => {  },
      err => { console.error(err); },
      () => {}
    );

    let notificationList = element.parentElement;
    notificationList.removeChild(element);
    this.checkForNotificationButton(notificationList);


  }

  /**
   * switches header notification button if there are more than 1 children
   * @param notificationList parentelement of the messages
   */
  checkForNotificationButton(notificationList){
    let button = document.getElementById('headerNotificationButton');
    if(notificationList.children.length > 1){
      let button = document.getElementById('headerNotificationButton');
      button.classList.remove('notificationIcon');
      button.classList.add('notificationIconNew');
    }
    else{
      if(button.classList.contains('notificationIconNew')){
        button.classList.remove('notificationIconNew');
        button.classList.add('notificationIcon');
      }
    }
  }

}
