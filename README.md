# textbanner

This is a Prestashop module that displays a text banner on the store front-end instead of using an image banner. It is much easier and practical to use simple HTML text with predefined colors. The module was developed by Vallka and is released under the Academic Free License (AFL 3.0).

The module extends the Module class and implements the WidgetInterface. It also has a private variable $write_log that can be used for logging.

The constructor sets the basic properties of the module such as name, tab, version, author, etc. It also sets the display name and description of the module that appears on the back office. Additionally, the minimum and maximum versions of Prestashop with which the module is compatible are set.

The install() method registers the displayBanner, actionObjectLanguageAddAfter, and backOfficeHeader hooks and installs fixtures. The fixtures are used to populate the TEXTBANNER_LINK and TEXTBANNER_DESC configuration variables. The backOfficeHeader() hook is used to add a JavaScript file to the back office, and the actionObjectLanguageAddAfter() hook is used to install the fixtures for each language.

The uninstall() method removes the TEXTBANNER_LINK and TEXTBANNER_DESC configuration variables when the module is uninstalled.

The postProcess() method is used to update the TEXTBANNER_LINK and TEXTBANNER_DESC configuration variables when the user saves the settings. The method updates the values for each language and clears the cache.

The hookBackOfficeHeader() method in the textbanner module is responsible for showing all input fields for all enabled languages simultaneously in the back office. Specifically, it adds the back.js file to the back office header when the configure parameter is set to the name of the textbanner module.

The purpose of back.js is to show all input fields for all enabled languages at once, on the same screen. This helps to remind the administrator to update all languages when making an update, and saves time by not having to switch between different languages to make the same change.

To achieve this, back.js simply changes the visibility of all input fields for all enabled languages to 'show'. This feature improves the user experience for administrators by providing a more efficient and convenient way to manage input fields for multiple languages.

## Licensing

This source file is subject to the Academic Free License (AFL 3.0)
that is available through the world-wide-web at this URL:
http://opensource.org/licenses/afl-3.0.php
If you did not receive a copy of the license and are unable to
obtain it through the world-wide-web, please send an email
to license@prestashop.com so we can send you a copy immediately.

## DISCLAIMER
 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
