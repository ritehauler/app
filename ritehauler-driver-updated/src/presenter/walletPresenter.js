// @flow
import { API_WALLET } from "../config/WebService";
import { PAGE_SIZE } from "../constant";
import Util from "../util";
import { UserPresenter } from "./";

class WalletPresenter {
  walletRequest;

  init(walletRequest) {
    this.walletRequest = walletRequest;
  }

  sendWalletRequest(currentPage: number = 1)  {
    const payload = {
      device_type: Util.getPlatform(),
      page: currentPage,
      page_size: PAGE_SIZE
    };

    this.walletRequest(API_WALLET, payload);
  }

  getWalletAmount(currencyCode: string, user: Object) {
    let amount = 0;
    const workers = UserPresenter.getUserProfile(user);
    if (workers && !Util.isEmpty(workers)) {
        amount = workers.security_deposit;
    }

    return Util.getCurrencyAmount(currencyCode, amount);
  }
}

export default new WalletPresenter();
