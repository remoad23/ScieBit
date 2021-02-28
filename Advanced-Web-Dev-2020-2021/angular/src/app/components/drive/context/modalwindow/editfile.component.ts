import {Component, ElementRef, Input, ViewChild, ViewContainerRef, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";
import {LoginService} from "../../../../Services/login.service";
import {SharedService} from "../../../../Services/shared.service";
import {FileComponent} from "../../assets/file/file.component";

@Component({
  selector: 'share',
  template: `
    <div #editModal class="modalWindowWrapper">
      <div class="modalWindow">
        <h1 i18n>Edit File</h1>
        <label i18n class="editLabel">Filename</label>
        <input #nameInput maxlength="50" class="filenameEdit" name="fileNameEdit" type="text" (change)=enableBtn() value="{{file.fileDetails.filename}}">
        <label i18n class="editLabel">Keywords</label>
        <div #keywordContainer class="keywordEditContainer">
            <div *ngFor="let keyword of file.fileDetails.keywords; index as i;" class="keywordEdit">
              {{keyword}}
              <i class="crossIcon keywordDeleteButton" (click)="removeKeyword(i)"></i>
            </div>
            <i #addKeywordButton class="plusIconBlue addKeywordButton" (click)="startKeywordInput()"></i>
        </div>
        <div class="keywordInputContainerWrapper">
          <div #keywordInputContainer class="keywordInputContainer">
               <input #keywordInput maxlength="25" class="keywordInput" type="text" name="addedKeyword" i18n-placeholder placeholder="Add Keyword...">
                <i class="plusIconBlue addKeywordButton" (click)="addKeyword()"></i>
          </div>

        </div>
        <div #errorMessage class="invisible" i18n>Keyword already exists!</div>
        <div #errorMessageCount class="invisible" i18n>Limited to 5 keywords!</div>
        <div #normalPlaceholder class="invisible" i18n>Add Keyword...</div>
        <div class="buttonVersioningWrapper">
          <button class="buttonSuccess" #submitBtn (click)="updateFile()" disabled i18n>Save</button>
          <button class="buttonCancel" (click)="cancel(true)" i18n>Cancel</button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .modalWindow {
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

    .modalWindowWrapper {
      width: 100vw;
      position: fixed;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.8);
      left: 0;
      top: 0;
      z-index: 1;
    }

    button {
      width: 32%;
      height: 2vw;
    }

    .buttonVersioningWrapper {
      display: flex;
      flex-direction: row;
      width: 100%;
      justify-content: space-evenly;
      margin-top: 20px;
    }

    .keywordEditContainer {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      width: 90%;
    }

    .keywordEdit {
      background-color: #36506C;
      color: white;
      border-radius: 10px;
      width: fit-content;
      margin: 1%;
      padding-left: 0.6vw;
      border: 2px solid #36506C;
      display: flex;
      align-items: center;
      justify-content: space-evenly;
      max-width: 100%;
      overflow: hidden;
    }

    .keywordDeleteButton {
      width: 1.5vw;
      height: 1.5vw;
      background-size: 50% !important;
      transition: 0.2s;
    }

    .keywordDeleteButton:hover {
      background-size: 60% !important;
      cursor: pointer;
    }

    .addKeywordButton {
      width: 1.5vw;
      height: 1.5vw;
      transition: 0.1s;
      border: 2px solid #36506C;
      border-radius: 50%;
      background-size: 60% !important;
      margin-left: 1%;
    }

    .addKeywordButton:hover {
      background-color: #36506C;
      background-image: url("./../assets/Images/Icons/PlusIcon_White.svg") !important;
      transform: scale(1.1);
      cursor: pointer;
    }

    .keywordInputContainerWrapper {
      width: 100%;
      height: 1.5vw;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 2%;
    }

    .keywordInputContainer {
      display: none;
      height: 100%;
      width: 60%;
      justify-content: center;
      align-items: center;
    }

    .keywordInput {
      height: 100%;
      border: 1px solid #36506C;
      border-radius: 5px;
    }

    .filenameEdit
    {
      height: 1.3vw;
      font-size: 0.9vw;
      margin-bottom: 3%;
      margin-top: 1%;
    }

    .errorMessage::placeholder {
      color: #980c12;
    }

    .invisible {
      display: none;
    }


    @media (max-width: 1270px) {
      .modalWindow {
        width: 40vw;
        left: 30%;
      }

      button {
        height: 3vw;
      }

      .filenameEdit {
        height: 2.5vw;
        font-size: 1.3vw;
      }

      .keywordDeleteButton {
        width: 3.4vw;
        height: 2.7vw;
      }

      .addKeywordButton {
        width: 2.7vw;
        height: 2.7vw;
      }

      .keywordInputContainerWrapper
      {
        height: 3vw;
      }
      .keywordInputContainer
      {
        width: 80%;
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

      .filenameEdit {
        height: 4.7vw;
        font-size: 3.3vw;
      }

      .keywordDeleteButton {
        width: 5.4vw;
        height: 5vw;
      }

      .addKeywordButton {
        width: 5.7vw;
        height: 5.7vw;
      }

      .editLabel
      {
        font-size: 2.9vw;
      }
      .keywordInputContainerWrapper {
        height: 5vw;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class EditfileComponent
{
  keywordCopy = [];
  nameCopy;
  deletedKeywords = [];
  addedKeywords = [];
  parentComponent: any;
  isUpdated;
  btnDisabled = true;
  file: FileComponent;
  userLock;
  @ViewChild('submitBtn') subbtn: ElementRef;
  @ViewChild('editModal',{read: ViewContainerRef}) editModal;
  @ViewChild('keywordContainer') keywordContainer: ElementRef;
  @ViewChild('keywordInput') keywordInput: ElementRef;
  @ViewChild('addKeywordButton') addKeywordButton: ElementRef;
  @ViewChild('keywordInputContainer') keywordInputContainer: ElementRef;
  @ViewChild('errorMessage') errorMessage: ElementRef;
  @ViewChild('errorMessageCount') errorMessageCount: ElementRef;
  @ViewChild('normalPlaceholder') normalPlaceholder: ElementRef;
  @ViewChild('nameInput') nameInput: ElementRef;

  constructor(private drive: DriveService,private router: Router,private login: LoginService,private share: SharedService) {
    this.file = null;
    this.isUpdated = {uploaded: false};
    this.parentComponent = null;
    this.nameCopy = '';
    this.userLock = {userType: "notFound"};
    this.login.getUserType(this.userLock);
    if(this.share.getObject("mousemenu") !== "NotFound")
    this.share.getObject('mousemenu')?.hideWhenInterActionClicked();
  }

  setFileComponent(fileComponent){
    this.file = fileComponent;
    for(let i = 0; i < this.file.fileDetails.keywords.length; i++){
      this.keywordCopy.push(this.file.fileDetails.keywords[i]);
      this.nameCopy = this.file.fileDetails.filename;
    }
  }

  /**
   * Updates the file
   */
  async updateFile(){
    let updatedName = null;
    let deletedKeywords = [];
    let addedKeywords = [];
    if(this.nameInput.nativeElement.value != this.file.fileDetails.filename){
      updatedName = this.nameInput.nativeElement.value;
    }
    for(let i = 0; i < this.keywordCopy.length; i++){
      if(this.file.fileDetails.keywords.find(element => element.toLowerCase() == this.keywordCopy[i].toLowerCase()) == undefined){
        deletedKeywords.push(this.keywordCopy[i]);
      }
    }
    for(let i = 0; i < this.file.fileDetails.keywords.length; i++){
      if(this.keywordCopy.find(element => element.toLowerCase() == this.file.fileDetails.keywords[i].toLowerCase()) == undefined){
        addedKeywords.push(this.file.fileDetails.keywords[i]);
      }
    }
    this.drive.updateFile(this.file.fileDetails.id,this.isUpdated,updatedName,deletedKeywords,addedKeywords);

    // wait till response from db with new created file
    while(!this.isUpdated.uploaded)
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    this.isUpdated.uploaded = false;
    this.file.fileDetails.filename = this.nameInput.nativeElement.value;

    this.cancel(false);
  }

  /**
   * shows the input element for the keyword input
   */
  startKeywordInput(){
    this.keywordInputContainer.nativeElement.style.display = 'flex';
    this.keywordContainer.nativeElement.removeChild(this.addKeywordButton.nativeElement);
  }

  /**
   * checks if the keyword is viable and adds it to the keyword list
   */
  addKeyword(){
    let keywordValue = this.keywordInput.nativeElement.value;

    if(keywordValue != ''){
      let keywordCollection = document.getElementsByClassName('keywordEdit');
      let keywordArray=[];
      for(let i = 0; i < keywordCollection.length; i++){
        keywordArray[i] = keywordCollection[i].firstChild.nodeValue.trim().toLowerCase();
      }
      // checks if the keyword already exists for the file
      let found = keywordArray.find(element => element == keywordValue.toLowerCase());
      // if there are already 5 keywords show error message
      if(keywordCollection.length >=5){
        this.keywordInput.nativeElement.placeholder = this.errorMessageCount.nativeElement.innerHTML;
        this.keywordInput.nativeElement.classList.toggle('errorMessage');
        this.keywordInput.nativeElement.value = '';
        setTimeout(()=>this.removeErrorMessage(), 3000);
      }
      else if(found == undefined){
        this.file.fileDetails.keywords.push(keywordValue);
        this.keywordInput.nativeElement.value = '';
        this.keywordInputContainer.nativeElement.style.display = 'none';
        this.errorMessage.nativeElement.display = 'none';
        this.keywordContainer.nativeElement.appendChild(this.addKeywordButton.nativeElement);
        this.enableBtn()
      }
      // if it exists shows error message
      else{
        this.keywordInput.nativeElement.placeholder = this.errorMessage.nativeElement.innerHTML;
        this.keywordInput.nativeElement.classList.toggle('errorMessage');
        this.keywordInput.nativeElement.value = '';
        setTimeout(()=>this.removeErrorMessage(), 3000);
      }
    }
    // close the input container
    else{
      this.keywordInput.nativeElement.value = '';
      this.keywordInputContainer.nativeElement.style.display = 'none';
      this.errorMessage.nativeElement.display = 'none';
      this.keywordContainer.nativeElement.appendChild(this.addKeywordButton.nativeElement);
    }

  }

  /**
   * removes the error message from the input element
   */
  removeErrorMessage(){
    this.keywordInput.nativeElement.placeholder = this.normalPlaceholder.nativeElement.innerHTML;
    this.keywordInput.nativeElement.classList.toggle('errorMessage');
  }

  /**
   * removes the clicked keyword element from the view
   */
  removeKeyword(index){
    this.file.fileDetails.keywords.splice(index,1);
    this.enableBtn();
  }

  /**
   * enables the submit button after a change
   */
  enableBtn(){
    if((this.nameCopy !== this.nameInput.nativeElement.value && this.nameInput.nativeElement.value != '')
      || this.deletedKeywords.length > 0 || this.addedKeywords.length > 0){
      this.subbtn.nativeElement.disabled = false;
      this.btnDisabled = false;
    }
    else{
      this.subbtn.nativeElement.disabled = true;
      this.btnDisabled = true;
    }
  }

  /**
   * get out of version modal window without saving
   * @param resetKeywords: determines whether the keywords should be resetted
   */
  cancel(resetKeywords)
  {
    if(resetKeywords){
      this.file.fileDetails.keywords = this.keywordCopy;
    }

    if(this.parentComponent != null){
      this.parentComponent.clicked = false;
      this.parentComponent.closeDetails();
    }
    if(this.share.getObject("mousemenu") !== "NotFound" && this.share.getObject("input") !== "NotFound")
    {
      this.share.getObject("mousemenu").modalWindowInitiated = false;
      this.share.getObject("mousemenu").input.remove(0);
      this.share.getObject("input").clear();
      this.share.removeObject("input");
    }
    else {
      //make object destroy itself
      this.editModal
        .element
        .nativeElement
        .parentElement
        .removeChild(this.editModal.element.nativeElement);
    }
  }

}
