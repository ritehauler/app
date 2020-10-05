// @flow
import React from "react";
import { View } from "react-native";
import moment from "moment";
import PropTypes from "prop-types";

import {
  TextInputContainer,
  DateTimeContainer,
  DateTimePicker,
  BoxError
} from "../../../appComponents";
import { Metrics } from "../../../theme";
import {
  DATE_FORMAT,
  DISPLAY_DATE_FORMAT,
  TIME_FORMAT,
  DISPLAY_TIME_FORMAT,
  DEFAULT_TIME
} from "../../../constant";
import styles from "./styles";

export default class Footer extends React.PureComponent {
  static propTypes = {
    onLayoutChange: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
  }

  _renderAddNote() {
    return (
      <TextInputContainer
        ref={ref => {
          this.noteInputContainer = ref;
        }}
        customContainerStyle={styles.noteContainer}
        title={"ADD Note"}
        placeHolder={"write something"}
        multiline
        onLayoutChange={this.props.onLayoutChange}
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        <View style={styles.separator} />
        {this._renderAddNote()}
      </View>
    );
  }
}
