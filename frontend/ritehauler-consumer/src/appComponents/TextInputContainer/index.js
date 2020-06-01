// @flow
import React from "react";
import { View, TextInput, ViewPropTypes } from "react-native";
import PropTypes from "prop-types";

import { Colors } from "../../theme";
import styles from "./styles";
import { Text, Separator } from "../../components";

export default class TextInputContainer extends React.PureComponent {
  static propTypes = {
    text: PropTypes.string,
    hideBox: PropTypes.bool,
    onChange: PropTypes.func,
    customContainerStyle: ViewPropTypes.style,
    title: PropTypes.string.isRequired,
    placeHolder: PropTypes.string.isRequired,
    multiline: PropTypes.bool,
    onLayoutChange: PropTypes.func
  };

  static defaultProps = {
    text: "",
    hideBox: false,
    onChange: undefined,
    customContainerStyle: {},
    multiline: false,
    onLayoutChange: () => {}
  };

  constructor(props) {
    super(props);
    this.onChangeOtherText = this.onChangeOtherText.bind(this);
  }

  state = {
    text: this.props.text,
    hideBox: this.props.hideBox
  };

  onChangeOtherText = text => {
    const { onChange } = this.props;
    this.setState({ text });
    if (onChange) {
      onChange(text);
    }
  };

  setText = text => {
    if (this.state.text !== text) {
      this.setState({ text });
    }
  };

  getText = () => this.state.text;

  setHideBox = hideBox => {
    if (this.state.hideBox !== hideBox) {
      this.setState({ hideBox, text: "" });
    }
  };

  _renderTitle() {
    const { title } = this.props;
    return (
      <Text style={styles.title} type="bold">
        {title}
      </Text>
    );
  }

  _renderSeparator() {
    return <Separator />;
  }

  _renderTextInput() {
    const { placeHolder, multiline } = this.props;
    const { text } = this.state;
    return (
      <TextInput
        autoCorrect={false}
        underlineColorAndroid="transparent"
        placeholder={placeHolder}
        placeholderTextColor={Colors.text.searchLabel}
        style={styles.textInput}
        value={text}
        onChangeText={this.onChangeOtherText}
        multiline={multiline}
        onLayout={this.props.onLayoutChange}
        selectionColor={Colors.text.accent}
      />
    );
  }

  render() {
    const { hideBox } = this.state;
    const { customContainerStyle } = this.props;
    if (hideBox) {
      return null;
    }
    return (
      <View style={[styles.container, customContainerStyle]}>
        {this._renderTitle()}
        {this._renderSeparator()}
        {this._renderTextInput()}
      </View>
    );
  }
}
