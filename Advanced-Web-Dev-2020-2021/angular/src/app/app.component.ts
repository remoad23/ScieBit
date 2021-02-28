import {
  AfterViewChecked, AfterViewInit,
  Component, ComponentFactory, ComponentFactoryResolver, HostListener,
  Inject,
  Injectable, Input,
  OnInit,
  ViewChild,
  ViewContainerRef,
  ViewEncapsulation
} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {DriveConfig} from "./components/drive.config";
import { ActivatedRoute } from '@angular/router';
import {LoginService} from "./Services/login.service";
import {DriveService} from "./Services/drive.service";
import {SharedService} from "./Services/shared.service";
import {NavigationStart, Router} from "@angular/router";
import {MousemenuComponent} from "./components/drive/context/mousemenu.component";
import {UploadfileComponent} from "./components/drive/context/modalwindow/uploadfile.component";
import {UploadfolderComponent} from "./components/drive/context/modalwindow/uploadfolder.component";
import {ShareComponent} from "./components/drive/context/modalwindow/share.component";
import {SharefolderComponent} from "./components/drive/context/modalwindow/sharefolder.component";
import {ConfirmdeleteComponent} from "./components/drive/context/modalwindow/confirmdelete.component";
import {VersionsComponent} from "./components/drive/context/modalwindow/versions.component";
import {MydocumentComponent} from "./components/drive/tables/my/mydocument.component";
import {DepartmentComponent} from "./components/drive/tables/department/departmentdocument.component";
import {ShareddocumentComponent} from "./components/drive/tables/shared/shareddocument.component";


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
  encapsulation: ViewEncapsulation.None
})

@Injectable()
export class AppComponent implements OnInit,AfterViewInit {

  private httpClient: HttpClient;
  private login: LoginService
  public fileOrFolderSelected: boolean;

  drive: DriveService;
  searchText = '';
  urlSet: boolean;
  pagination;
  modalwindow;
  modalWindowFactory: ComponentFactory<ConfirmdeleteComponent
    | ShareComponent
    | UploadfileComponent
    | UploadfolderComponent
    | SharefolderComponent
    | VersionsComponent>;
  factoryResolver;
  // currentPath inside leftContainer div to use to navigate
  currentPath;


  @ViewChild('documentcontainer',{read: ViewContainerRef}) input;
  private contextMenu;
  mousemenuFactory: ComponentFactory<MousemenuComponent>;

  // Dependency Injection for httpClient
  constructor(private http: HttpClient,
              private _login: LoginService,
              private _drive: DriveService,
              private share: SharedService,
              public router: Router,
              private componentFactoryResolver: ComponentFactoryResolver,
              private route: ActivatedRoute) {
    this.httpClient = http;
    this.login = _login;
    this.drive = _drive;
    this.urlSet = false;
    this.pagination = {index: 0};
    this.mousemenuFactory = this.componentFactoryResolver.resolveComponentFactory(MousemenuComponent);
    this.fileOrFolderSelected = false;
    this.factoryResolver = componentFactoryResolver;
    this.currentPath = [];
    this.share.insertObject("app",this);

    router.events.subscribe((event) => {
      if (event instanceof NavigationStart)
        this.updatePath(event);
    });
  }

  ngOnInit() {
    this.login.init();
    this.initDocuments();
  }

  ngAfterViewInit() {
    document.getElementById('getMoreNotifcations')
      .addEventListener('click',()=>{this.getNotifications()});

  }

  async initDocuments(){
    while(!(this.login.isLogin()))
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    if(!this.urlSet){
      (document.getElementById('userNav') as HTMLAnchorElement).href = "http://localhost/Advanced-Web-Dev-2020-2021/public/User/" + this.login.id;
      (document.getElementById('headerUserNav') as HTMLAnchorElement).href = "http://localhost/Advanced-Web-Dev-2020-2021/public/User/" + this.login.id;
      if(this.login.share !== "NotFound"){
        document.getElementById('shareButton').click();
      }
      else{
        document.getElementById('myButton').click();
      }
      this.getNotifications();
    }
  }


  getNotifications()
  {
    this.drive.getNotifications(this.pagination);
  }

  sortByName(){
    if(this.router.url === "/my")
              this.share.getObject("mydocument").sortByName();
            else if(this.router.url === "/department")
              this.share.getObject("departmentdocument").sortByName();
            else if(this.router.url === "/shared")
              this.share.getObject("shareddocument").sortByName();
            else if(this.router.url.match("/folder") != null)
              this.share.getObject("currentFolderContent").sortByName();
  }

  sortByDate(){
      if(this.router.url === "/my")
                this.share.getObject("mydocument").sortByDate();
              else if(this.router.url === "/department")
                this.share.getObject("departmentdocument").sortByDate();
              else if(this.router.url === "/shared")
                this.share.getObject("shareddocument").sortByDate();
              else if(this.router.url.match("/folder"))
                this.share.getObject("currentFolderContent").sortByDate();
  }

  searchFiles(){
      if(this.router.url === "/my")
          this.share.getObject("mydocument").searchFiles(this.searchText);
      else if(this.router.url === "/department")
          this.share.getObject("departmentdocument").searchFiles(this.searchText);
      else if(this.router.url === "/shared")
          this.share.getObject("shareddocument").searchFiles(this.searchText);
      else if(this.router.url.match("/folder"))
        this.share.getObject("currentFolderContent").searchFiles(this.searchText);

  }

  openMouseMenu(event)
  {
    // remove previous mousemenu from sharedservice container if new click has occured
    if(this.share.getObject("input") !== "NotFound" )
    {
      this.share.getObject("input").remove(0);
      this.share.removeObject("input");
      this.share.insertObject("input",this.input);
    }
    else
    {
      this.share.insertObject("input",this.input);
    }

    //create mousemenu
    this.contextMenu = this.input.createComponent(this.mousemenuFactory);

    // give file as a reference for the mousemenu

    if(this.share?.getObject("selectedFolder")?.folderID)
    {
      this.contextMenu.instance.folder = this.share.getObject("selectedFile");
    }
    else if(this.share?.getObject("selectedFile")?.fileID)
    {
      this.contextMenu.instance.file = this.share.getObject("selectedFile");
    }

    //set mousemenu to the mouse coordinates
    this.contextMenu.instance.hostElement.style.top = `${event.pageY + "px"}`;

    //if the mouse menu would be outside of the screen offset it into the screen width
    let width = this.contextMenu.instance.hostElement.offsetWidth;
    if(event.pageX + width < window.innerWidth){
      this.contextMenu.instance.hostElement.style.left = `${event.pageX + "px"}`;
    }
    else{
      this.contextMenu.instance.hostElement.style.left = `${window.innerWidth - width - 30 + "px"}`;
    }

    // returning false makes the default dropdown of the webbrowser not appear
    return false;
  }

  /**
   * if clicking outside the mousemenu will be destroyed
   */
  destroyMouseMenu()
  {
    if(this.share.getObject("mousemenu").modalWindowInitiated) return;
    let mousemenu = this.share.getObject("mousemenu");
    let input = this.share.getObject("input");

    if(!(mousemenu === "NotFound" || input === "NotFound"))
    {
      input.remove(0);
      this.share.removeObject("input");
    }



    // get rid of folder/file selection when clicking somewhere outside left mouse click
    if(this.share?.getObject("selectedFolder")?.folderID )
    {
      this.share.getObject("selectedFolder").unselectFolder();
    }
    else if(this.share?.getObject("selectedFile")?.fileID)
    {
      this.share.getObject("selectedFile").unselectFile();
    }

  }

  /**
   * moves the mouse menu into the window
   */
  moveMouseMenu()
  {
    if(this.share.getObject("input") !== "NotFound" && this.share.getObject("mousemenu") !== "NotFound")
    {
      let mousemenu = this.share.getObject("mousemenu").hostElement;

      //if the mouse menu would be outside of the screen offset it into the screen width
      let width = mousemenu.offsetWidth;
      if(mousemenu.offsetLeft + 20 + width >= window.innerWidth){
        mousemenu.style.left = `${window.innerWidth - width - 30 + "px"}`;
      }
    }
  }


  createFile()
  {
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(UploadfileComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
  }

  createFolder()
  {
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(UploadfolderComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
  }

  delete()
  {
    let selectedFile = this.share.getObject("selectedFile");
    let selectedFolder = this.share.getObject("selectedFolder");
    if(selectedFile === "NotFound" && selectedFolder !== "NotFound")
    {
      this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(ConfirmdeleteComponent);
      this.modalwindow = this.input.createComponent(this.modalWindowFactory);
      this.modalwindow.instance.folderID = selectedFolder.folderID;
    }
    if(selectedFile !== "NotFound" && selectedFolder === "NotFound")
    {
      this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(ConfirmdeleteComponent);
      this.modalwindow = this.input.createComponent(this.modalWindowFactory);
      this.modalwindow.instance.fileID = selectedFile.fileID;
    }
  }

  download()
  {
    let selectedFile = this.share.getObject("selectedFile");
    let selectedFolder = this.share.getObject("selectedFolder");
    let url = '';
    if(selectedFile === "NotFound" && selectedFolder !== "NotFound")
    {
      if(this.router.url === "/shared"){
        url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/shareddocument/folder/download/${this.login.id}/${this.login.token}/${selectedFolder.folderID}`;
      }
      else{
        url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/mydocument/${this.login.id}/${this.login.token}/${selectedFolder.folderID}/folder/download`;
      }
      window.location.href = url;
      return;
    }
    if(selectedFile !== "NotFound" && selectedFolder === "NotFound")
    {
      if(this.router.url === "/shared"){
        url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/shareddocument/${this.login.id}/${this.login.token}/${selectedFile.fileID}/${selectedFile.hashName}/download`;
      }
      else{
        url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/mydocument/${this.login.id}/${this.login.token}/${selectedFile.hashName}/download`;
      }
      window.location.href = url;
      return;
    }
  }



  shareSelected()
  {
    let selectedFile = this.share.getObject("selectedFile");
    let selectedFolder = this.share.getObject("selectedFolder");
    if(selectedFile === "NotFound" && selectedFolder !== "NotFound")
    {
      this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(SharefolderComponent);
      this.modalwindow = this.input.createComponent(this.modalWindowFactory);
      this.modalwindow.instance.folderID = selectedFolder.folderID;
    }
    if(selectedFile !== "NotFound" && selectedFolder === "NotFound")
    {
      this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(ShareComponent);
      this.modalwindow = this.input.createComponent(this.modalWindowFactory);
      this.modalwindow.instance.fileID = selectedFile.fileID;
    }
  }

  @HostListener('window:resize', ['$event'])
  onResize() {
    this.moveMouseMenu();
  }

  /**
   * updates the path inside the leftcontainer div by using the
   * urls information
   */
  updatePath(event)
  {

    // folderid
    let id = "";
    // my,shared or department
    let section = "";
    // name of folder
    let name = "";

    if(event.url.includes('my'))
    {
      section = "/my";
      name = "My Documents"
      id = event.url?.split("my/folder/")?.[1];
    }

    else if(event.url.includes('department'))
    {
      section = "/department";
      name = "Department Documents"
      id = event.url?.split("department/folder/")?.[1];
    }

    else if(event.url.includes('shared'))
    {
      section = "/shared";
      name = "Shared Documents"
      id = event.url?.split("shared/folder/")?.[1];
    }

    if(event.url === "/my")
    {
      this.currentPath = [];
      this.currentPath.push({url: section, name: name});
      return;
    }
    else if(event.url === "/department")
    {
      this.currentPath = [];
      this.currentPath.push({url: section, name: name});
      return;
    }
    else if(event.url === "/shared")
    {
      this.currentPath = [];
      this.currentPath.push({url: section, name: name});
      return;
    }

    // if a id cant be found in url
    if(!id) return;

    this.currentPath = [];
    this.currentPath.push({url: section, name: name});

    // trace back to root folder
    this.drive.getFolderRoute(id,this.currentPath,section);

  }


  /**
   * Event when a folder is entered while an element is getting dragged
   * @param event dragEvent
   * @param folder entered folder
   */
  dragEnter(event: DragEvent, url){
    let folderId = this.getRouteId(url);
    let urlId = this.getRouteId(this.router.url);
    if(folderId !== urlId){
      let currentComponent = this.getCurrentPathComponent();
      if((<Element>event.target).classList != null){
        if(currentComponent.dragElement != null){
          if((<Element>event.target).classList.contains("pathButton")){
            currentComponent.dropFolder = {id: folderId};
            currentComponent.draggedOverFolderElement = (<Element>event.target).parentElement;
            currentComponent.draggedOverFolderElement.classList.add("draggedOverPath");
          }
        }
      }
    }
  }

  /**
   * Event when a folder is no longer hovered while an element is getting dragged
   * @param event dragEvent
   */
  dragLeave(event: DragEvent){
    event.preventDefault();
    let currentComponent = this.getCurrentPathComponent();
    if(currentComponent.draggedOverFolderElement != null){
      currentComponent.draggedOverFolderElement.classList.remove("draggedOverPath");

      currentComponent.draggedOverFolderElement = null;
      currentComponent.dropFolder = null;
    }
  }

  /**
   * Event when a folder is hovered while an element is getting dragged
   * @param event dragEvent
   */
  allowDrop(event: DragEvent){
    event.preventDefault();
  }

  /**
   * output the current component the user is currently inside
   * @private
   */
  private getCurrentPathComponent()
  {
    if(this.router.url.includes('/folder/'))
      return this.share.getObject("currentFolderContent")
    else if(this.router.url.includes('my'))
      return this.share.getObject("mydocument")
    else if(this.router.url.includes('department'))
      return this.share.getObject("departmentdocument")
    else if(this.router.url.includes('shared'))
      return this.share.getObject("shareddocument")
  }

  private getRouteId(url)
  {
    if(url.includes('my'))
      return url?.split("my/folder/")?.[1];
    else if(url.includes('department'))
      return url?.split("department/folder/")?.[1];
    else if(url.includes('shared'))
       return url?.split("shared/folder/")?.[1];
  }

}
