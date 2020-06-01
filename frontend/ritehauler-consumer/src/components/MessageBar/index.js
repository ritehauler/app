import React from "react";
import { MessageBar, MessageBarManager } from "react-native-message-bar";
import { Colors, Metrics, Fonts } from "../../theme";

export default class extends React.Component {
  componentDidMount() {
    MessageBarManager.registerMessageBar(this.refs.alert);
  }

  componentWillUnmount() {
    MessageBarManager.unregisterMessageBar();
  }

  styles = {
    error: {
      backgroundColor: Colors.background.error,
      strokeColor: Colors.background.error,
      titleColor: Colors.text.tertiary,
      messageColor: Colors.text.tertiary
    },
    success: {
      backgroundColor: Colors.background.info,
      strokeColor: Colors.background.info,
      titleColor: Colors.text.tertiary,
      messageColor: Colors.text.tertiary
    },
    messageStyle: {
      fontSize: Fonts.size.small,
      fontFamily: Fonts.type.base
    }
  };

  render() {
    const { error, success, messageStyle } = this.styles;
    return (
      <MessageBar
        ref="alert"
        stylesheetError={error}
        stylesheetSuccess={success}
        viewTopInset={Metrics.statusBarHeight}
        messageStyle={messageStyle}
      />
    );
  }
}
