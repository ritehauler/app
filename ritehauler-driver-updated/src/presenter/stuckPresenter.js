// @flow
import { API_STUCK_REASON_LIST } from "../config/WebService";
import Util from "../util";
import { STUCK_ASSIGNMENT_MODE } from "../constant";

class StuckPresenter {

  sendStuckListRequest(stuckRequest)  {
    const payload = {
      device_type: Util.getPlatform()
    };

    stuckRequest(API_STUCK_REASON_LIST, payload);
  }

  getAssignmentModeObject(data: Object) {
    return (data && !Util.isEmpty(data) && data.modes && !Util.isEmpty(data.modes)) 
        ? data.modes
        : {};
  }

  isAssignmentMode(data: Object) {
    const assignmentMode = this.getAssignmentModeObject(data);

    return (!Util.isEmpty(assignmentMode) && assignmentMode.assignment_mode) 
        ? assignmentMode.assignment_mode === STUCK_ASSIGNMENT_MODE.AUTOMATIC
        : false;
  }

  getAssignmentMode(data: Object) {
    const assignmentMode = this.getAssignmentModeObject(data);

    return (!Util.isEmpty(assignmentMode) && assignmentMode.assignment_mode) 
        ? assignmentMode.assignment_mode 
        : "";
  }

  getStuckList(data: Object) {
    return (data && !Util.isEmpty(data) && data.reasons && !Util.isEmpty(data.reasons)) 
            ? data.reasons 
            : [];
  }

  getName(stuckItem: Object) {
    return (stuckItem && !Util.isEmpty(stuckItem) && stuckItem.name)
        ? stuckItem.name
        : "";
  }

  getId(stuckItem: Object) {
    return (stuckItem && !Util.isEmpty(stuckItem) && stuckItem.id)
        ? stuckItem.id
        : "";
  }

  getTypeable(stuckItem: Object) {
    return (stuckItem && !Util.isEmpty(stuckItem) && stuckItem.typeable)
        ? Util.toInt(stuckItem.typeable)
        : 0;
  }

  getStuckReason(isOtherSelected: boolean, inputText: Object, stuckItem: Object) {
    return (isOtherSelected) 
      ? inputText.getText()
      : this.getName(stuckItem);
  }
}

export default new StuckPresenter();
