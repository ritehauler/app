// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";

import { Strings } from "../../../theme";
import styles from "./styles";
import { Text } from "../../../components";
import AddImages from "../AddImages";
import { EXPENSIVE_ITEM_MINIMUM_PRICE } from "../../../constant";

export default class ItemReceipt extends React.PureComponent {
  static propTypes = {
    showReceipt: PropTypes.bool
  };

  static defaultProps = { showReceipt: true };

  state = { showReceipt: this.props.showReceipt };

  setReceiptDisplay = showReceipt => {
    this.setState({ showReceipt });
  };

  getReceiptDisplay() {
    return this.state.showReceipt;
  }

  getReceiptImages() {
    return this.receiptImage.getUploadedImagesArray();
  }

  _renderItemReceptImages() {
    return (
      <AddImages
        ref={ref => (this.receiptImage = ref)}
        title={Strings.addItemReceipt}
        imagesArray={this.props.imagesArray}
      />
    );
  }

  _renderExpensiveInfo() {
    const info = Strings.expensiveItemInfo.replace(
      "$$",
      `$${EXPENSIVE_ITEM_MINIMUM_PRICE}`
    );
    return (
      <Text type="light" size="small" style={styles.info}>
        {info}
      </Text>
    );
  }

  render() {
    const { showReceipt } = this.state;
    if (showReceipt) {
      return (
        <View>
          {this._renderItemReceptImages()}
          {this._renderExpensiveInfo()}
        </View>
      );
    }
    return null;
  }
}
