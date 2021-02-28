import {
  Component,
  ComponentFactory,
  ComponentFactoryResolver,
  Input, OnInit,
  ViewChild,
  ViewContainerRef,
  ViewEncapsulation
} from '@angular/core';
import {MousemenuComponent} from "../../context/mousemenu.component";
import {SharedService} from "../../../../Services/shared.service";
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";

@Component({
  selector: 'folder',
  template: `
    <div  class="fileContainer" #fileElement (click)="selectFolder();$event.stopPropagation()" (contextmenu)="$event.stopPropagation();openMouseMenu($event)">
      <div class="imgLeftFolder"></div>
      <div class="textFolder">
        <ng-content></ng-content>
      </div>
      <i class="menuFolderIcon" (click)="$event.stopPropagation();openCircleMenu($event)"></i>
    </div>`,
  styles: [`
    folder
  {
      height: 2vw;
      width: 8.7%;
      margin-right: 4%;
      display: flex;
      flex-flow: row;
      flex-shrink: 0;
  }
    .fileContainer{
      height: 100%;
      width: 100%;
      flex-flow: row;
      display: flex;
      font-weight: 500;
      border: 1px solid black;
    }
    .imgLeftFolder
    {
      height: 100%;
      width: 35%;
      background-repeat: no-repeat;
      background-size: 62%;
      background-position: center;
      background-image: url("./../assets/Images/Icons/FolderIcon.svg");
      pointer-events: none;
    }
    .textFolder{
      height: 100%;
      width: 58%;
      display: flex;
      flex-flow: column;
      justify-content: center;
      padding-left: 5%;
      pointer-events: none;
      overflow-wrap: anywhere;
      overflow: hidden;
      text-overflow: ellipsis;
      font-size: 0.9vw;
    }
    .menuFolderIcon{
      background-image: url("./assets/Images/Icons/circleMenuIcon.svg");
      display: none;
      background-repeat: no-repeat;
      background-size: 20%;
      background-position: center;
      cursor: pointer;
    }

    @media (max-width: 1270px) {
      folder {
        height: 4vw;
        width: 20.7%;
        margin-bottom: 3%;
      }
      .textFolder{
        font-size: 1em;
      }
    }

    @media (max-width: 600px) {
      folder {
        height: 7.5vw;
        width: 45.8%;
        margin-bottom: 5%;
      }
      .imgLeftFolder{
        background-size: 52%;
      }
      .menuFolderIcon{
        display: block;
        width: 20%;
      }
    }
  `],
  entryComponents: [MousemenuComponent],
  encapsulation: ViewEncapsulation.None,
})

export class FolderComponent implements OnInit  {

  @Input() folderID: number;
  @Input() hashName : string;
  private contextMenu;
  private urlPath;
  public locked: boolean;

  @ViewChild('fileElement',{read: ViewContainerRef}) input;

  mousemenuFactory: ComponentFactory<MousemenuComponent>;

  constructor(private componentFactoryResolver: ComponentFactoryResolver,private shared: SharedService,private router: Router) {

    this.mousemenuFactory = this.componentFactoryResolver.resolveComponentFactory(MousemenuComponent);
    this.locked = false;
  }

  ngOnInit()
  {
    this.urlPath = "my/folder/"
  }

  openMouseMenu(event)
  {
    this.selectFolder();
    // remove previous mousemenu from sharedservice container if new click has occured
    if(this.shared.getObject("input") !== "NotFound" )
    {
      this.shared.getObject("input").remove(0);
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
    this.contextMenu.instance.folder = this;


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
  openCircleMenu(event){
    this.selectFolder();
    // remove previous mousemenu from sharedservice container if new click has occured
    if(this.shared.getObject("input") !== "NotFound" )
    {
      this.shared.getObject("input").remove(0);
      this.shared.removeObject("input");
    }
    else
    {
      this.shared.insertObject("input",this.input);

      //create mousemenu
      this.contextMenu = this.input.createComponent(this.mousemenuFactory);

      // give file as a reference for the mousemenu
      this.contextMenu.instance.folder = this;

      //set mousemenu to the menuIcon coordinates - the menu width
      let width = this.contextMenu.instance.hostElement.offsetWidth;

      this.contextMenu.instance.hostElement.style.left = `${event.target.offsetLeft-width + "px"}`;
      this.shared.getObject("app").fileOrFolderSelected = true;

    }
  }

  /**
   * set this component active visually and save it in sharedfolder for mousemenu
   */
  selectFolder()
  {
    this.locked = true;

    let selectedFile = this.shared.getObject("selectedFile");
    let selectedFolder = this.shared.getObject("selectedFolder");

    // if no previous file has been clicked
    if(selectedFile === "NotFound" && selectedFolder === "NotFound")
    {
      this.input.element.nativeElement.style.backgroundColor = "#C6E5F3";
      this.shared.insertObject("selectedFolder",this);
    }
    // folder is clicked already
    else if(selectedFolder !== "NotFound")
    {
      selectedFolder.input.element.nativeElement.style.backgroundColor = 'transparent';
      this.shared.removeObject("selectedFolder");
      this.input.element.nativeElement.style.backgroundColor = "#C6E5F3";
      this.shared.insertObject("selectedFolder",this);
    }
    // file clicked already
    else{
      selectedFile.input.element.nativeElement.parentElement.style.backgroundColor = 'transparent';
      this.shared.removeObject("selectedFile");
      this.input.element.nativeElement.style.backgroundColor = "#C6E5F3";
      this.shared.insertObject("selectedFolder",this);
    }
    this.shared.getObject("app").fileOrFolderSelected = true;
  }


  unselectFolder()
  {
    this.input.element.nativeElement.style.backgroundColor = 'transparent';
    this.shared.removeObject("selectedFolder");
    this.shared.getObject("app").fileOrFolderSelected = false;
  }
}
