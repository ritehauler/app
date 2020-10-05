// @flow
import React from "react";
import { View } from "react-native";
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
    if (this.props.addItem && this.props.cbOnAddItemPress) {
      this.props.cbOnAddItemPress();
    }
  }

  _renderSeparator() {
    return <View style={styles.separator} />;
  }

  _renderHeader() {
    return (
      <View style={styles.content}>
        <Text type="bold" style={styles.itemNames}>
          {this.props.title}
        </Text>
        {this.props.addItem && (
          <Text size="small" color="accent">
            {this.props.addItem}
          </Text>
        )}
      </View>
    );
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
