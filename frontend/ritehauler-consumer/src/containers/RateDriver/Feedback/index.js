// @flow
import React from "react";
import { TextInput } from "react-native";
import PropTypes from "prop-types";

import { Colors, Strings } from "../../../theme";
import styles from "./styles";

export default class Feedback extends React.PureComponent {
  static propTypes = {
    feedback: PropTypes.string,
    onLayoutChange: PropTypes.func.isRequired
  };

  static defaultProps = { feedback: "" };

  constructor(props) {
    super(props);
    this.onValueChange = this.onValueChange.bind(this);
  }

  state = {
    feedback: this.props.feedback
  };

  onValueChange(feedback) {
    this.setState({
      feedback
    });
  }

  getFeedback = () => this.state.feedback;

  render() {
    return (
      <TextInput
        placeholder={Strings.feedbackPlaceholder}
        multiline
        underlineColorAndroid="transparent"
        style={styles.textInput}
        value={this.state.feedback}
        onChangeText={this.onValueChange}
        onLayout={this.props.onLayoutChange}
        selectionColor={Colors.accent}
      />
    );
  }
}
