// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";

import DataHandler from "../../../util/DataHandler";
import { Strings } from "../../../theme";
import styles from "./styles";
import { Text } from "../../../components";
import AddImages from "../AddImages";

export default class ItemReceipt extends React.PureComponent {
  static propTypes = {
    showReceipt: PropTypes.bool,
    imagesArray: PropTypes.array
  };

  static defaultProps = { showReceipt: false, imagesArray: [] };

  state = { showReceipt: this.props.showReceipt };

  setReceiptDisplay = showReceipt => {
    this.setState({ showReceipt });
  };

  getShowReceipt = () => this.state.showReceipt;

  getReceiptImages = () => {
    if (this.state.showReceipt) {
      return this.receiptImages.getUploadedImagesArray();
    }
    return [];
  };

  _renderItemReceptImages() {
    return (
      <AddImages
        title={Strings.addItemReceipt}
        ref={ref => {
          this.receiptImages = ref;
        }}
        imagesArray={this.props.imagesArray}
      />
    );
  }

  _renderExpensiveInfo() {
    const info = Strings.expensiveItemInfo.replace(
      "==",
      `${DataHandler.currency()}${DataHandler.expensiveItemCost()}`
    );
    return (
      <Text type="light" size="xSmall" style={styles.info}>
        {info}
      </Text>
    );
  }

  render() {
    const { showReceipt } = this.state;
    if (showReceipt) {
      return (
        <View style={styles.container}>
          {this._renderItemReceptImages()}
          {this._renderExpensiveInfo()}
        </View>
      );
    }
    return null;
  }
}
