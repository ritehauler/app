
// @flow
import _ from "lodash";
import Util from "../util";

class AppDataPresenter {

    setOrderAcceptStartTime(appData: Object, orderStartTime: string = "") {
        const newAppData = _.cloneDeep(appData);
        if (appData && !Util.isEmpty(appData)) {
            newAppData.orderAcceptStartTime = orderStartTime;
        }

        return newAppData
    }

    getOrderAcceptStartTime(appData: Object) {
        return (appData && !Util.isEmpty(appData) && appData.orderAcceptStartTime && !Util.isEmpty(appData.orderAcceptStartTime))
            ? appData.orderAcceptStartTime
            : Util.getCurrentDateTime();
    }
}

export default new AppDataPresenter();
