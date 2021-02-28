import {
  Component,
  ComponentFactory,
  ComponentFactoryResolver, ElementRef,
  HostListener,
  Input,
  ViewChild,
  ViewContainerRef,
  ViewEncapsulation
} from '@angular/core';
import {MousemenuComponent} from "../../context/mousemenu.component";
import {FiledetailsComponent} from "../../context/filedetails.component";
import {SharedService} from "../../../../Services/shared.service";
import {Router} from "@angular/router";

@Component({
  selector: 'file',
  template: `
    <div #fileElement class="fileWrapper" (click)="selectFile();openFileDetails($event);$event.stopPropagation()"  (contextmenu)="$event.stopPropagation();openMouseMenu($event)">
      <div class="fileImageContainer"><div *ngIf="fileDetails.version_count > 0 && (router.url === '/my' || router.url.startsWith('/my/folder'))" class="versionCount">{{fileDetails.version_count}}</div></div>
      <p class="fileText">
        <ng-content></ng-content>
      </p>
      <div #fileDetailButton class="fileDetailButtonContainer">
         <i class="arrowIcon fileDetailButton"></i>
      </div>
      <p class="uploadDate">
        {{fileDetails.created_at | date: 'dd/MM/yyyy'}}
      </p>
      <i class="menuIcon" (click)="$event.stopPropagation();openCircleMenu($event)"></i>

     </div>`,
  styles: [`
    file
    {
      height: 7vw;
      width: 8.55%;
      margin-right: 4%;
      display: flex;
      border: 1px solid black;
      flex-flow: column;
      flex-shrink: 0;
      flex-grow: 0;
      transition: max-height 1s;
    }
    .fileImageContainer
    {
      display: flex;
      height: 75%;
      width: 100%;
      justify-content: flex-end;
      align-items: flex-end;
      background-image: url("./assets/Images/Icons/FileIcon.svg");
      background-repeat: no-repeat;
      background-size: 50%;
      background-position: center;
    }
    .versionCount
    {
      width: 1.2vw;
      height: 1.2vw;
      border-radius: 50%;
      border: 2px solid #36506C;
      background-color: white;
      color: #36506C;
      margin: 1vw;
      margin-bottom: 0.4vw;
      display: flex;
      justify-content: center;
      align-items: center;
      pointer-events: none;
    }
    .fileWrapper{
      height: 100%;
      width: 100%;
    }
    .fileText
    {
      width: 100%;
      height: 25%;
      text-align: center;
      display: flex;
      margin: 0;
      justify-content: center;
      align-items: center;
      border-top: 1px solid black;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      pointer-events: none;
    }
    .uploadDate{
      display: none;
    }
    .menuIcon{
      background-image: url("./assets/Images/Icons/circleMenuIcon.svg");
      display: none;
      background-repeat: no-repeat;
      background-size: 20%;
      background-position: center;
      cursor: pointer;
    }
    .fileDetailButtonContainer
    {
      display: none;
    }

    @media (max-width: 1270px) {
      file {
        height: 17vw;
        width: 20.35%;
        margin-bottom: 2%;
      }

      .versionCount {
        width: 2.2vw;
        height: 2.2vw;
        margin: 3.3vw;
        margin-bottom: 0.9vw;
      }
    }

    @media (max-width: 800px) {
      .versionCount {
        width: 3.2vw;
        height: 3.2vw;
        margin: 3.3vw;
        margin-bottom: 0.5vw;
      }
    }

    @media (max-width: 600px) {
      file {
        height: 15vw;
        width: 95%;
        margin-bottom: 2%;
      }
      .fileWrapper
      {
        display: flex;
        height: 15vw;
      }
      .fileText {
        width: 40%;
        height: 100%;
        justify-content: flex-start;
        border: none;
        font-size: 4vw;
      }
      .fileImageContainer {
        height: 100%;
        width: 20%;
        background-size: 50%;
      }
      .uploadDate{
        height: 100%;
        width: 30%;
        margin: 0;
        text-align: center;
        display: flex;
        align-items: center;
        font-size: 4vw;
      }
      .menuIcon{
        display: block;
        width: 10%;
      }
      .versionCount {
        width: 3.8vw;
        height: 3.8vw;
        margin: 2.4vw;
        margin-bottom: 1.3vw;
      }
      .fileDetailButtonContainer {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 6%;
        margin-right: 7%;
        margin-left: -13%;
        align-items: center;
        justify-content: flex-end;
      }

      .fileDetailButton {
        margin-bottom: 30%;
        width: 80%;
        cursor: pointer;
      }
      .invisible
      {
        display: none;
      }

      .fileDetailsOpened{
        height: auto;
      }

    }
  `],
  entryComponents: [MousemenuComponent],
  encapsulation: ViewEncapsulation.None,
})

export class FileComponent
{

  @Input() fileID: number;
  @Input() hashName : string;
  @Input() version_group_id : number;
  @Input() fileDetails: any;
  router;
  public contextMenu;
  public filedetailsMenu;
  public locked: boolean;

  @ViewChild('fileElement',{read: ViewContainerRef}) public input;
  @ViewChild('fileDetailButton') fileDetailButton : ElementRef;

  mousemenuFactory: ComponentFactory<MousemenuComponent>;
  filedetailsFactory: ComponentFactory<FiledetailsComponent>;

  constructor(private componentFactoryResolver: ComponentFactoryResolver,private shared: SharedService, private routerR: Router) {

    this.mousemenuFactory = this.componentFactoryResolver.resolveComponentFactory(MousemenuComponent);
    this.filedetailsFactory = this.componentFactoryResolver.resolveComponentFactory(FiledetailsComponent);
    this.router = routerR;
    this.locked = false;
  }

  openMouseMenu(event)
  {
    this.selectFile();
    // remove previous mousemenu from sharedservice container if new click has occured
    if(this.shared.getObject("input") !== "NotFound" )
    {
      this.shared.getObject("input").clear();
      this.shared.removeObject("input");
      this.shared.insertObject("input",this.input);
    }
    else
    {
      this.shared.insertObject("input",this.input);
    }

    //create mousemenu
    this.contextMenu = this.input.createComponent(this.mousemenuFactory);

    // give file as a reference for the mousemenu
    this.contextMenu.instance.file = this;

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


    this.shared.getObject("app").fileOrFolderSelected = true;

    // returning false makes the default dropdown of the webbrowser not appear
    return false;
  }

  /**
   * opens the mouse menu from circle menu button
   * @param event: click event
   */
  openCircleMenu(event)
  {
    this.selectFile();
    // remove previous mousemenu from sharedservice container if new click has occured
    if(this.shared.getObject("input") !== "NotFound" )
    {
      this.shared.getObject("input").clear();
      this.shared.removeObject("input");
    }
    else
    {
      this.shared.insertObject("input",this.input);
      //create mousemenu
      this.contextMenu = this.input.createComponent(this.mousemenuFactory);

      // give file as a reference for the mousemenu
      this.contextMenu.instance.file = this;

      let width = this.contextMenu.instance.hostElement.offsetWidth;

      //set mousemenu to the menuIcon coordinates - the menu width
      this.contextMenu.instance.hostElement.style.left = `${event.target.offsetLeft-width + "px"}`;
      this.shared.getObject("app").fileOrFolderSelected = true;

    }

  }

  /**
   * opens the filedetails component to show the details of the file
   */
  openFileDetails(event){

    // remove previous mousemenu from sharedservice container if new click has occured
    if(this.shared.getObject("input") !== "NotFound" )
    {
      this.shared.getObject("input").clear();
      this.shared.removeObject("input");
    }

    //create filedetails
    this.filedetailsMenu = this.input.createComponent(this.filedetailsFactory);

    // give file as a reference for the filedetails
    this.filedetailsMenu.instance.file = this;

    if(window.innerWidth > 600){
      // calculates the position of filedetails to be perfectly centered on the file component
      let detailsWidth = this.filedetailsMenu.instance.hostElement.offsetWidth;

      let fileWidth = event.target.offsetWidth;
      let fileLeft = event.target.offsetLeft;

      let detailsLeft = fileLeft + ((fileWidth - detailsWidth)/2);

      let detailsHeight = this.filedetailsMenu.instance.hostElement.offsetHeight;

      let fileHeight = event.target.parentElement.offsetHeight;
      let fileTop = event.target.offsetTop;

      let detailsTop = fileTop + ((fileHeight - detailsHeight)/2);


      //set filedetails to the calculated coordinates
      this.filedetailsMenu.instance.hostElement.style.left = `${detailsLeft + "px"}`;
      this.filedetailsMenu.instance.hostElement.style.top = `${detailsTop + "px"}`;
    }
    else{
      this.input.element.nativeElement.parentElement.classList.add('fileDetailsOpened');
      this.filedetailsMenu.instance.clicked = true;
      this.fileDetailButton.nativeElement.classList.add("invisible");
    }
  }

  /**
   * closes the file details
   */
  closeFileDetails(){
    this.input.element.nativeElement.parentElement.classList.remove('fileDetailsOpened');
    this.fileDetailButton.nativeElement.classList.remove("invisible");
  }

  /**
   * set this component active visually and save it in shared for mousemenu
   */
  selectFile()
  {
    this.locked = true;
    this.shared.getObject("app").fileOrFolderSelected = true;
    let selectedFile = this.shared.getObject("selectedFile");
    let selectedFolder = this.shared.getObject("selectedFolder");

    // if no previous file has been clicked
    if(selectedFile === "NotFound" && selectedFolder === "NotFound")
    {
      this.input.element.nativeElement.parentElement.style.backgroundColor = "#C6E5F3";
      this.shared.insertObject("selectedFile",this);
    }
    // folder already active
    else if(selectedFolder !== "NotFound")
    {
      selectedFolder.input.element.nativeElement.style.backgroundColor = 'transparent';
      this.shared.removeObject("selectedFolder");
      this.input.element.nativeElement.parentElement.style.backgroundColor = "#C6E5F3";
      this.shared.insertObject("selectedFile",this);
    }
    // file already active
    else{
      selectedFile.input.element.nativeElement.parentElement.style.backgroundColor = 'transparent';
      this.shared.removeObject("selectedFile");
      this.input.element.nativeElement.parentElement.style.backgroundColor = "#C6E5F3";
      this.shared.insertObject("selectedFile",this);
    }
  }


  unselectFile()
  {
    this.input.element.nativeElement.parentElement.style.backgroundColor = 'transparent';
    this.shared.removeObject("selectedFile");
    this.shared.getObject("app").fileOrFolderSelected = false;
  }
}
