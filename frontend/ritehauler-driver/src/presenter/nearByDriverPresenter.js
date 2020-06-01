// @flow
import { API_NEAR_BY_DRIVER } from "../config/WebService";
import Util from "../util";

class NearByDriverPresenter {
    nearByDriverRequest;

    init(nearByDriverRequest) {
        this.nearByDriverRequest = nearByDriverRequest;
    }

    sendNearByDriverRequest(location: Object)  {
        const payload = {
            latitude: location.latitude,
            longitude: location.longitude,
            date: Util.getCurrentDateTimeInGMT(),
            device_type: Util.getPlatform()
        };

        this.nearByDriverRequest(API_NEAR_BY_DRIVER, payload);
    }

    getNearByDriverList(data: Object) {
        return (data && !Util.isEmpty(data) && data.nearby && !Util.isEmpty(data.nearby))
            ? data.nearby
            : [];
    }

    getWorker(workerItem: Object) {
        return (workerItem && !Util.isEmpty(workerItem) && workerItem.workers && !Util.isEmpty(workerItem.workers))
            ? workerItem.workers
            : {};
    }

    getWorkerLocation(workerItem: Object) {
        const worker = this.getWorker(workerItem);
        return (!Util.isEmpty(worker) && worker.geo && !Util.isEmpty(worker.geo))
            ? worker.geo
            : {};
    }

    getWorkerDocument(workerItem: Object) {
        const worker = this.getWorker(workerItem);
        return (!Util.isEmpty(worker) && worker.documents && !Util.isEmpty(worker.documents))
            ? worker.documents
            : {};
    }

    getWorkerVehicle(workerItem: Object) {
        const worker = this.getWorker(workerItem);
        return (!Util.isEmpty(worker) && worker.vehicle && !Util.isEmpty(worker.vehicle))
            ? worker.vehicle
            : {};
    }

    getId(workerItem: Object) {
        const worker = this.getWorker(workerItem);

        return (!Util.isEmpty(worker) && worker.id && !Util.isEmpty(worker.id))
            ? worker.id
            : "";
    }

    getName(workerItem: Object) {
        const worker = this.getWorker(workerItem);

        return (!Util.isEmpty(worker) && 
                (worker.first_name && !Util.isEmpty(worker.first_name)) || 
                (worker.last_name && !Util.isEmpty(worker.last_name)))
            ? worker.first_name + " " + worker.last_name
            : "";
    }

    getNamePrefix(workerItem: Object) {
        const worker = this.getWorker(workerItem);
        let title = "TT";
        if (!Util.isEmpty(worker) && 
            (worker.first_name && !Util.isEmpty(worker.first_name)) || 
            (worker.last_name && !Util.isEmpty(worker.last_name))) {
          const { first_name, last_name } = worker;
          title = first_name.charAt(0) + last_name.charAt(0);
        }
    
        return title;
    }

    getVehicleName(workerItem: Object) {
        const vehicle = this.getWorkerVehicle(workerItem);
        return (!Util.isEmpty(vehicle) && vehicle.name && !Util.isEmpty(vehicle.name))
            ? vehicle.name
            : "";
    }

    getDistance(workerItem: Object) {
        const location = this.getWorkerLocation(workerItem);
        return (!Util.isEmpty(location) && location.distance && !Util.isEmpty(location.distance) && location.distance.text)
            ? location.distance.text
            : "";
    }

    getUserProfileHashId(workerItem: Object) {
        const workerDocument = this.getWorkerDocument(workerItem);
        return (!Util.isEmpty(workerDocument) && workerDocument.hash_id && !Util.isEmpty(workerDocument.hash_id))
            ? workerDocument.hash_id
            : "";
    }

    getUserImageSource(workerItem: Object, defaultImage: Object) {
        const userHashId = this.getUserProfileHashId(workerItem);
        // const token = this.getToken(data);
        return Util.getImageSource(userHashId, defaultImage);
    }
}

export default new NearByDriverPresenter();
