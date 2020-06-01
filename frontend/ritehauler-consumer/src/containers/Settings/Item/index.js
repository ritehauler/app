// @flow
import React from "react";
import { View, Switch } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text } from "../../../components";

export default class Item extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired,
    onToggle: PropTypes.func.isRequired,
    index: PropTypes.number.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onToggleValueChanged = this.onToggleValueChanged.bind(this);
  }

  onToggleValueChanged(toggleValue) {
    this.props.onToggle(this.props.index, toggleValue);
  }

  render() {
    const { title, isOn } = this.props.data;
    return (
      <View style={styles.container} onPress={this.onPressItem}>
        <Text style={{ flex: 1 }}>{title}</Text>
        <Switch onValueChange={this.onToggleValueChanged} value={isOn} />
      </View>
    );
  }
}
