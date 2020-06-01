// @flow
import React from "react";
import { View } from "react-native";
import { Actions } from "react-native-router-flux";

import styles from "./styles";
import { Text, ButtonView, Separator } from "../../../components";
import { Strings } from "../../../theme";
import DataHandler from "../../../util/DataHandler";

export default class Header extends React.PureComponent {
  constructor(props) {
    super(props);
    this.addItemAction = this.addItemAction.bind(this);
  }

  addItemAction() {
    Actions.addItem();
    DataHandler.setFirstItem(false);
  }

  _renderHeader() {
    return (
      <View style={styles.content}>
        <Text type="bold" style={styles.itemNames}>
          {Strings.itemsName}
        </Text>
        <Text size="small" color="accent">
          {Strings.addItem}
        </Text>
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={this.addItemAction}>
        {this._renderHeader()}
        {this._renderSeparator()}
      </ButtonView>
    );
  }
}
